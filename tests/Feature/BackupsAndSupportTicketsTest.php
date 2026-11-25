<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\SupportReply;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\TenantBackup;
use App\Models\User;
use App\Services\TenantBackupService;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Interior\App\Models\Project;
use Tests\TestCase;

class BackupsAndSupportTicketsTest extends TestCase
{
    protected array $createdTenants = [];

    protected array $createdUsers = [];

    protected function tearDown(): void
    {
        $manager = app(TenantDatabaseManager::class);
        $manager->disconnect();

        foreach ($this->createdTenants as $tenant) {
            $backups = TenantBackup::where('tenant_id', $tenant->id)->get();
            foreach ($backups as $b) {
                $full = storage_path('app/' . $b->file_path);
                if (File::exists($full)) {
                    @File::delete($full);
                }
                $b->delete();
            }

            try {
                $manager->dropDatabase($tenant);
            } catch (\Throwable) {
            }
            Domain::where('tenant_id', $tenant->id)->delete();
            $tenant->forceDelete();
        }

        foreach ($this->createdUsers as $user) {
            $tickets = SupportTicket::where('user_id', $user->id)->get();
            foreach ($tickets as $tkt) {
                SupportReply::where('ticket_id', $tkt->id)->delete();
                $tkt->delete();
            }
            $user->delete();
        }

        parent::tearDown();
    }

    public function test_per_tenant_database_snapshot_backup_one_click_restore_and_support_ticket_workflow(): void
    {
        $manager = app(TenantDatabaseManager::class);
        $backupService = app(TenantBackupService::class);
        $suffix = strtolower(Str::random(5));

        $superAdmin = User::create([
            'type' => 'super_admin',
            'name' => 'Ops Super Admin',
            'email' => "ops_sa_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $superAdmin;

        $client = User::create([
            'type' => 'client',
            'name' => 'Ops Client',
            'email' => "ops_client_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $client;

        $tenant = Tenant::create([
            'owner_id' => $client->id,
            'theme_module' => 'Interior',
            'name' => 'Backup Restore Studio',
            'subdomain' => 'bkp-' . $suffix,
            'status' => 'pending_verification',
            'db_name' => 'hub_test_bkp_' . $suffix,
            'email_verified_at' => now(),
        ]);
        $this->createdTenants[] = $tenant;

        $tenant = $manager->provisionTenant($tenant, false, 'active');

        // 1. Verify tenant-scoped cache prefix is set when connecting to tenant
        $manager->connect($tenant);
        $this->assertEquals('hub_t' . $tenant->id . '_', config('cache.prefix'));

        // 2. Seed an original project in the tenant's isolated DB
        Project::create([
            'title' => 'Original Restorable Villa',
            'slug' => 'original-restorable-villa',
            'category' => 'Luxury',
            'is_active' => true,
        ]);
        $this->assertEquals(1, Project::where('slug', 'original-restorable-villa')->count());

        // 3. Create a per-tenant database snapshot backup via Dashboard controller
        $bkpRes = $this->actingAs($client)->post(route('admin.backups.store'), [
            'tenant_id' => $tenant->id,
            'notes' => 'Pre-change snapshot',
        ]);
        $bkpRes->assertRedirect();

        $backup = TenantBackup::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($backup);
        $this->assertTrue(File::exists(storage_path('app/' . $backup->file_path)));

        // 4. Delete the project and add a wrong project in the tenant's DB
        $manager->connect($tenant);
        Project::query()->delete();
        Project::create([
            'title' => 'Accidental Mistake Project',
            'slug' => 'accidental-mistake',
            'is_active' => true,
        ]);
        $this->assertEquals(0, Project::where('slug', 'original-restorable-villa')->count());

        // 5. One-click Restore from Backup -> Original project returns and mistake is wiped!
        $restoreRes = $this->actingAs($client)->post(route('admin.backups.restore', $backup->id));
        $restoreRes->assertRedirect();

        $manager->connect($tenant);
        $this->assertEquals(1, Project::where('slug', 'original-restorable-villa')->count());
        $this->assertEquals(0, Project::where('slug', 'accidental-mistake')->count());

        // 6. Support Ticket Workflow: Client opens ticket -> Super Admin replies -> Status becomes 'answered' -> Close
        $tktRes = $this->actingAs($client)->post(route('admin.support.tickets.store'), [
            'subject' => 'Need help with custom domain DNS',
            'category' => 'domain',
            'priority' => 'high',
            'tenant_id' => $tenant->id,
            'message' => 'Please check my TXT record verification.',
        ]);
        $tktRes->assertRedirect();

        $ticket = SupportTicket::where('user_id', $client->id)->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('open', $ticket->status);

        $replyRes = $this->actingAs($superAdmin)->post(route('admin.support.tickets.reply', $ticket->id), [
            'message' => 'We verified your TXT record and activated Caddy SSL!',
        ]);
        $replyRes->assertRedirect();

        $this->assertEquals('answered', $ticket->fresh()->status);
        $this->assertEquals(2, $ticket->replies()->count());

        $closeRes = $this->actingAs($client)->post(route('admin.support.tickets.close', $ticket->id));
        $closeRes->assertRedirect();
        $this->assertEquals('closed', $ticket->fresh()->status);
    }
}
