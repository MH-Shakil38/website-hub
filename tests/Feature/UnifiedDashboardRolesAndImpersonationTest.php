<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class UnifiedDashboardRolesAndImpersonationTest extends TestCase
{
    protected array $createdTenants = [];

    protected array $createdUsers = [];

    protected function tearDown(): void
    {
        $manager = app(TenantDatabaseManager::class);
        $manager->disconnect();

        foreach ($this->createdTenants as $tenant) {
            try {
                $manager->dropDatabase($tenant);
            } catch (\Throwable) {
            }
            Domain::where('tenant_id', $tenant->id)->delete();
            $tenant->forceDelete();
        }

        foreach ($this->createdUsers as $user) {
            try {
                $user->delete();
            } catch (\Throwable) {
            }
        }

        parent::tearDown();
    }

    public function test_role_conditional_sidebar_and_tenant_scoping_for_super_admin_reseller_and_client(): void
    {
        $suffix = strtolower(Str::random(5));

        $superAdmin = User::create([
            'type' => 'super_admin',
            'name' => 'Test Super Admin',
            'email' => "sa_{$suffix}@example.com",
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);
        $this->createdUsers[] = $superAdmin;

        $reseller = User::create([
            'type' => 'reseller',
            'name' => 'Test Reseller Partner',
            'email' => "reseller_{$suffix}@example.com",
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);
        $this->createdUsers[] = $reseller;

        $clientUnderReseller = User::create([
            'type' => 'client',
            'reseller_id' => $reseller->id,
            'name' => 'Reseller Client One',
            'email' => "client1_{$suffix}@example.com",
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);
        $this->createdUsers[] = $clientUnderReseller;

        $otherDirectClient = User::create([
            'type' => 'client',
            'name' => 'Direct Client Two',
            'email' => "client2_{$suffix}@example.com",
            'password' => Hash::make('password'),
            'status' => 'Active',
        ]);
        $this->createdUsers[] = $otherDirectClient;

        $tenant1 = Tenant::create([
            'owner_id' => $clientUnderReseller->id,
            'reseller_id' => $reseller->id,
            'theme_module' => 'Interior',
            'name' => 'Client One Interior Site',
            'subdomain' => 'c1-' . $suffix,
            'status' => 'active',
            'db_name' => 'hub_test_c1_' . $suffix,
        ]);
        $this->createdTenants[] = $tenant1;

        $tenant2 = Tenant::create([
            'owner_id' => $otherDirectClient->id,
            'theme_module' => 'Pharmacy',
            'name' => 'Client Two Pharmacy Site',
            'subdomain' => 'c2-' . $suffix,
            'status' => 'active',
            'db_name' => 'hub_test_c2_' . $suffix,
        ]);
        $this->createdTenants[] = $tenant2;

        // 1. Super Admin can access both tenants and sees Super Admin Panel badge
        $this->assertTrue($superAdmin->canAccessTenant($tenant1));
        $this->assertTrue($superAdmin->canAccessTenant($tenant2));
        $saResponse = $this->actingAs($superAdmin)->get(route('admin.dashboard'));
        $saResponse->assertStatus(200);
        $saResponse->assertSee('Super Admin Panel');

        // 2. Reseller can access own client's tenant ($tenant1) but NOT $tenant2
        $this->assertTrue($reseller->canAccessTenant($tenant1));
        $this->assertFalse($reseller->canAccessTenant($tenant2));
        $resResponse = $this->actingAs($reseller)->get(route('admin.dashboard'));
        $resResponse->assertStatus(200);
        $resResponse->assertSee('Reseller Partner Panel');

        // 3. Client can only access own tenant ($tenant1) and sees Client Website Panel badge
        $this->assertTrue($clientUnderReseller->canAccessTenant($tenant1));
        $this->assertFalse($clientUnderReseller->canAccessTenant($tenant2));
        $clientResponse = $this->actingAs($clientUnderReseller)->get(route('admin.dashboard'));
        $clientResponse->assertStatus(200);
        $clientResponse->assertSee('Client Website Panel');

        // 4. Client attempting to switch to another client's tenant gets 403 Forbidden
        $forbiddenSwitch = $this->actingAs($clientUnderReseller)
            ->get(route('admin.tenants.switch', $tenant2->id));
        $forbiddenSwitch->assertStatus(403);
    }

    public function test_super_admin_and_reseller_impersonation_flow(): void
    {
        $suffix = strtolower(Str::random(5));

        $superAdmin = User::create([
            'type' => 'super_admin',
            'name' => 'Main Super Admin',
            'email' => "sa_imp_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $superAdmin;

        $reseller = User::create([
            'type' => 'reseller',
            'name' => 'Agency Reseller',
            'email' => "res_imp_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $reseller;

        $ownClient = User::create([
            'type' => 'client',
            'reseller_id' => $reseller->id,
            'name' => 'Reseller Own Client',
            'email' => "own_c_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $ownClient;

        $strangerClient = User::create([
            'type' => 'client',
            'name' => 'Stranger Client',
            'email' => "stranger_c_{$suffix}@example.com",
            'password' => Hash::make('password'),
        ]);
        $this->createdUsers[] = $strangerClient;

        // Reseller can impersonate ownClient
        $res = $this->actingAs($reseller)->post(route('admin.impersonate.start', $ownClient->id));
        $res->assertRedirect(route('admin.dashboard'));
        $this->assertEquals($ownClient->id, auth()->id());
        $this->assertEquals($reseller->id, session('impersonator_id'));

        // Return from impersonation back to Reseller
        $leave = $this->post(route('admin.impersonate.leave'));
        $leave->assertRedirect(route('admin.dashboard'));
        $this->assertEquals($reseller->id, auth()->id());

        // Reseller CANNOT impersonate strangerClient (403)
        $denied = $this->actingAs($reseller)->post(route('admin.impersonate.start', $strangerClient->id));
        $denied->assertStatus(403);

        // Super Admin CAN impersonate anyone
        $saImp = $this->actingAs($superAdmin)->post(route('admin.impersonate.start', $strangerClient->id));
        $saImp->assertRedirect(route('admin.dashboard'));
        $this->assertEquals($strangerClient->id, auth()->id());
    }
}
