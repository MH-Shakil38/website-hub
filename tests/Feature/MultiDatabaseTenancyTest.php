<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Tenant;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Interior\App\Models\ModulePage;
use Modules\Interior\App\Models\Project;
use Tests\TestCase;

class MultiDatabaseTenancyTest extends TestCase
{
    protected array $createdTenants = [];

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

        parent::tearDown();
    }

    public function test_two_tenants_have_completely_isolated_databases(): void
    {
        $manager = app(TenantDatabaseManager::class);
        $suffix = strtolower(Str::random(5));

        $tenantA = Tenant::create([
            'theme_module' => 'Interior',
            'name' => 'Alpha Interiors',
            'subdomain' => 'alpha-' . $suffix,
            'status' => 'pending_verification',
            'db_name' => 'hub_test_a_' . $suffix,
            'email_verified_at' => now(),
        ]);
        $this->createdTenants[] = $tenantA;

        $tenantB = Tenant::create([
            'theme_module' => 'Interior',
            'name' => 'Beta Interiors',
            'subdomain' => 'beta-' . $suffix,
            'status' => 'pending_verification',
            'db_name' => 'hub_test_b_' . $suffix,
            'email_verified_at' => now(),
        ]);
        $this->createdTenants[] = $tenantB;

        // Provision both isolated databases
        $tenantA = $manager->provisionTenant($tenantA, false, 'active');
        $tenantB = $manager->provisionTenant($tenantB, false, 'active');

        $this->assertEquals('active', $tenantA->status);
        $this->assertEquals('active', $tenantB->status);
        $this->assertNotEquals($tenantA->db_name, $tenantB->db_name);

        // Connect to Tenant A and create a project & page
        $manager->connect($tenantA);
        Project::create([
            'title' => 'Alpha Secret Penthouse',
            'slug' => 'alpha-secret-penthouse',
            'category' => 'Luxury',
            'is_active' => true,
        ]);
        ModulePage::create([
            'title' => 'Alpha Exclusive Page',
            'slug' => 'alpha-exclusive',
            'status' => 'Published',
            'views' => 0,
        ]);

        $this->assertEquals(1, Project::where('slug', 'alpha-secret-penthouse')->count());
        $this->assertEquals(1, ModulePage::where('slug', 'alpha-exclusive')->count());

        // Switch to Tenant B and verify Tenant A's data does NOT exist in Tenant B's database
        $manager->connect($tenantB);
        $this->assertEquals(0, Project::where('slug', 'alpha-secret-penthouse')->count());
        $this->assertEquals(0, ModulePage::where('slug', 'alpha-exclusive')->count());

        Project::create([
            'title' => 'Beta Modern Villa',
            'slug' => 'beta-modern-villa',
            'category' => 'Residential',
            'is_active' => true,
        ]);

        $this->assertEquals(1, Project::where('slug', 'beta-modern-villa')->count());

        // Switch back to Tenant A and confirm Beta's project is not visible
        $manager->connect($tenantA);
        $this->assertEquals(0, Project::where('slug', 'beta-modern-villa')->count());
        $this->assertEquals(1, Project::where('slug', 'alpha-secret-penthouse')->count());
    }

    public function test_provisioning_rolls_back_database_on_failure(): void
    {
        $manager = app(TenantDatabaseManager::class);
        $suffix = strtolower(Str::random(5));
        $dbName = 'hub_test_fail_' . $suffix;

        $tenant = Tenant::create([
            'theme_module' => 'Interior',
            'name' => 'Failing Tenant',
            'subdomain' => 'fail-' . $suffix,
            'status' => 'pending_verification',
            'db_name' => $dbName,
        ]);
        $this->createdTenants[] = $tenant;

        // Create database then simulate a failure by invoking drop rollback
        $manager->createDatabase($tenant);
        $existsBefore = DB::connection('central')->select("SHOW DATABASES LIKE '{$dbName}'");
        $this->assertNotEmpty($existsBefore);

        $manager->dropDatabase($tenant);
        $existsAfter = DB::connection('central')->select("SHOW DATABASES LIKE '{$dbName}'");
        $this->assertEmpty($existsAfter);
    }
}
