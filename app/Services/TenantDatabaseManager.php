<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Tenant;
use App\Models\TenantMigrationLog;
use App\Support\ModuleCmsResolver;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class TenantDatabaseManager
{
    protected ?Tenant $activeTenant = null;

    /**
     * Get the currently connected tenant (if any).
     */
    public function currentTenant(): ?Tenant
    {
        if ($this->activeTenant) {
            return $this->activeTenant;
        }

        return app()->bound('tenant') ? app('tenant') : null;
    }

    /**
     * Dynamically switch the 'tenant' database connection to the given Tenant's database.
     */
    public function connect(Tenant $tenant): void
    {
        $dbName = $tenant->db_name ?: $tenant->generateDefaultDbName();
        $dbHost = $tenant->db_host ?: config('database.connections.central.host', '127.0.0.1');
        $dbPort = $tenant->db_port ?: config('database.connections.central.port', '3306');
        $dbUser = $tenant->db_username ?: config('database.connections.central.username', 'root');
        $dbPass = $tenant->db_password !== null
            ? $tenant->db_password
            : config('database.connections.central.password', '');

        config([
            'database.connections.tenant.host' => $dbHost,
            'database.connections.tenant.port' => $dbPort,
            'database.connections.tenant.database' => $dbName,
            'database.connections.tenant.username' => $dbUser,
            'database.connections.tenant.password' => $dbPass,
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        $this->activeTenant = $tenant;
        app()->instance('tenant', $tenant);
    }

    /**
     * Disconnect the current tenant and reset the 'tenant' connection back to central default.
     */
    public function disconnect(): void
    {
        $centralDb = config('database.connections.central.database');

        config([
            'database.connections.tenant.database' => $centralDb,
        ]);

        DB::purge('tenant');
        $this->activeTenant = null;

        if (app()->bound('tenant')) {
            app()->forgetInstance('tenant');
        }
    }

    /**
     * Create the physical MySQL database for a tenant.
     */
    public function createDatabase(Tenant $tenant): string
    {
        $dbName = $tenant->db_name ?: $tenant->generateDefaultDbName();

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
            throw new \InvalidArgumentException("Invalid tenant database name [{$dbName}].");
        }

        DB::connection('central')->statement(
            "CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        if ($tenant->db_name !== $dbName) {
            $tenant->update(['db_name' => $dbName]);
        }

        return $dbName;
    }

    /**
     * Drop the physical MySQL database for a tenant.
     */
    public function dropDatabase(Tenant $tenant): void
    {
        $dbName = $tenant->db_name;
        if (!$dbName || !preg_match('/^[a-zA-Z0-9_]+$/', $dbName)) {
            return;
        }

        $centralDb = config('database.connections.central.database');
        if ($dbName === $centralDb) {
            throw new \RuntimeException('Refusing to drop the central database.');
        }

        if ($this->activeTenant && $this->activeTenant->id === $tenant->id) {
            $this->disconnect();
        }

        DB::connection('central')->statement("DROP DATABASE IF EXISTS `{$dbName}`");
    }

    /**
     * Run a module's migrations on the connected tenant database.
     */
    public function migrateModule(Tenant $tenant, ?string $module = null): void
    {
        $mod = $module ?: $tenant->theme_module;
        $migrationDir = base_path("Modules/{$mod}/Database/Migrations");

        $this->connect($tenant);

        if (!File::isDirectory($migrationDir)) {
            return;
        }

        $relativePath = 'Modules/' . $mod . '/Database/Migrations';

        try {
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => $relativePath,
                '--force' => true,
            ]);

            TenantMigrationLog::create([
                'tenant_id' => $tenant->id,
                'module' => $mod,
                'migration' => $relativePath,
                'status' => 'success',
                'ran_at' => now(),
            ]);
        } catch (Throwable $e) {
            TenantMigrationLog::create([
                'tenant_id' => $tenant->id,
                'module' => $mod,
                'migration' => $relativePath,
                'status' => 'failed',
                'error' => $e->getMessage(),
                'ran_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Seed default demo data for a tenant's theme module.
     */
    public function seedModuleDemoData(Tenant $tenant, ?string $module = null): void
    {
        $mod = $module ?: $tenant->theme_module;
        $this->connect($tenant);

        // Trigger module-specific data service seeders if present
        $serviceMap = [
            'Portfolio' => \Modules\Portfolio\App\Services\PortfolioDataService::class,
            'HajjUmrah' => \Modules\HajjUmrah\App\Services\HajjUmrahDataService::class,
            'GymFitness' => \Modules\GymFitness\App\Services\GymFitnessDataService::class,
            'WonderLearn' => \Modules\WonderLearn\App\Services\WonderLearnDataService::class,
            'EventManagement' => \Modules\EventManagement\App\Services\EventManagementDataService::class,
        ];

        if (isset($serviceMap[$mod]) && class_exists($serviceMap[$mod])) {
            app($serviceMap[$mod])->ensureSeeded();
        }

        $seederClass = "Modules\\{$mod}\\Database\\Seeders\\TenantDemoSeeder";
        if (class_exists($seederClass)) {
            app($seederClass)->run();
        }

        // Ensure isolated tenant upload folder exists
        $tenantUploadDir = base_path("Modules/{$mod}/Public/uploads/t{$tenant->id}");
        File::ensureDirectoryExists($tenantUploadDir);
    }

    /**
     * Copy existing tables for a module from the central database into the tenant database
     * (used when migrating existing single-DB demo data into dedicated demo tenant DBs).
     */
    public function copyModuleTablesFromCentral(Tenant $tenant, string $module): int
    {
        $meta = ModuleCmsResolver::moduleMeta($module);
        $prefix = preg_replace('/[^a-zA-Z0-9_]/', '', $meta['prefix']);
        $centralDb = config('database.connections.central.database');
        $tenantDb = $tenant->db_name;

        $this->connect($tenant);

        $tables = DB::connection('central')->select(
            "SHOW TABLES FROM `{$centralDb}` LIKE '{$prefix}%'"
        );

        $copied = 0;
        foreach ($tables as $row) {
            $tableName = (string) current((array) $row);
            if (!Schema::connection('tenant')->hasTable($tableName)) {
                continue;
            }

            $countInTenant = DB::connection('tenant')->table($tableName)->count();
            $countInCentral = DB::connection('central')->table($tableName)->count();

            if ($countInTenant === 0 && $countInCentral > 0) {
                $tenantCols = Schema::connection('tenant')->getColumnListing($tableName);
                $centralCols = Schema::connection('central')->getColumnListing($tableName);
                $commonCols = array_values(array_intersect($tenantCols, $centralCols));

                if (!empty($commonCols)) {
                    $colList = implode(', ', array_map(fn ($c) => "`{$c}`", $commonCols));
                    DB::connection('central')->statement(
                        "INSERT INTO `{$tenantDb}`.`{$tableName}` ({$colList}) SELECT {$colList} FROM `{$centralDb}`.`{$tableName}`"
                    );
                    $copied++;
                }
            }
        }

        return $copied;
    }

    /**
     * Full provisioning pipeline for a verified tenant:
     * 1. Status -> provisioning
     * 2. Ensure primary subdomain record exists
     * 3. Create physical MySQL database
     * 4. Run theme module migrations
     * 5. Seed demo data & create isolated upload directory
     * 6. Status -> trial/active (or rollback on failure)
     */
    public function provisionTenant(Tenant $tenant, bool $copyFromCentralIfDemo = false, string $finalStatus = 'trial'): Tenant
    {
        $tenant->update(['status' => 'provisioning']);

        $baseDomain = config('app.central_domains')[0] ?? 'multi-website-hub.test';
        $fullSubdomain = str_contains($tenant->subdomain, '.')
            ? $tenant->subdomain
            : ($tenant->subdomain . '.' . $baseDomain);

        Domain::firstOrCreate(
            ['domain' => $fullSubdomain],
            [
                'tenant_id' => $tenant->id,
                'type' => 'subdomain',
                'is_primary' => true,
                'verified_at' => now(),
                'ssl_status' => 'active',
            ]
        );

        try {
            $this->createDatabase($tenant);
            $this->migrateModule($tenant, $tenant->theme_module);

            if ($copyFromCentralIfDemo) {
                $this->copyModuleTablesFromCentral($tenant, $tenant->theme_module);
            }

            $this->seedModuleDemoData($tenant, $tenant->theme_module);

            $tenant->update([
                'status' => $finalStatus,
                'trial_ends_at' => $finalStatus === 'trial' ? ($tenant->trial_ends_at ?? now()->addDays(14)) : $tenant->trial_ends_at,
            ]);

            return $tenant->fresh();
        } catch (Throwable $e) {
            try {
                $this->dropDatabase($tenant);
            } catch (Throwable) {
                // Ignore drop error during rollback
            }

            $tenant->update(['status' => 'failed']);
            throw $e;
        }
    }
}
