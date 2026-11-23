<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('demo:activate {module}', function (string $module) {
    $modulePath = base_path("Modules/{$module}");
    if (!File::isDirectory($modulePath)) {
        $this->error("Module [{$module}] does not exist in Modules/ directory!");
        return 1;
    }

    // 1. Update modules_statuses.json (set only target module to true)
    $statusFile = base_path('modules_statuses.json');
    $statuses = File::exists($statusFile) ? json_decode(File::get($statusFile), true) : [];
    foreach ($statuses as $key => $val) {
        $statuses[$key] = false;
    }
    $statuses[$module] = true;
    File::put($statusFile, json_encode($statuses, JSON_PRETTY_PRINT));

    // 2. Update ACTIVE_MODULE in .env
    $envPath = base_path('.env');
    if (File::exists($envPath)) {
        $envContent = File::get($envPath);
        if (preg_match('/^ACTIVE_MODULE=/m', $envContent)) {
            $envContent = preg_replace('/^ACTIVE_MODULE=.*/m', "ACTIVE_MODULE={$module}", $envContent);
        } else {
            $envContent .= "\nACTIVE_MODULE={$module}\n";
        }
        File::put($envPath, $envContent);
    }

    // Sync database core_websites table
    if (class_exists(\App\Models\Website::class)) {
        \App\Models\Website::query()->update(['is_active' => false]);
        \App\Models\Website::where('module_name', $module)->update(['is_active' => true]);
    }

    // 3. Recreate public/module-assets symlink pointing to Modules/{module}/Public
    $linkPath = public_path('module-assets');
    $targetPath = base_path("Modules/{$module}/Public");
    File::ensureDirectoryExists($targetPath . '/assets');
    File::ensureDirectoryExists($targetPath . '/uploads');

    if (is_link($linkPath) || File::exists($linkPath)) {
        @unlink($linkPath);
        @rmdir($linkPath);
    }
    app('files')->link($targetPath, $linkPath);

    // 4. Clear Caches
    Artisan::call('optimize:clear');
    $this->info("Active module switched to [{$module}] and assets linked successfully!");
})->purpose('Activate a target module and link its public assets');

Artisan::command('demo:handover {module} {--force}', function (string $module) {
    if (!$this->option('force')) {
        $this->warn("Use --force to permanently delete all modules except [{$module}].");
        return 1;
    }

    Artisan::call('demo:activate', ['module' => $module]);

    $directories = File::directories(base_path('Modules'));
    foreach ($directories as $dir) {
        $dirName = basename($dir);
        if ($dirName !== $module) {
            File::deleteDirectory($dir);
            $this->line("Removed unused module folder: Modules/{$dirName}");
        }
    }

    File::put(base_path('modules_statuses.json'), json_encode([$module => true], JSON_PRETTY_PRINT));
    Artisan::call('optimize:clear');
    $this->info("Client Handover Ready! Only [{$module}] module remains in the system.");
})->purpose('Prepare client handover by keeping only the specified module');

// =========================================================================
// SAAS MULTI-DATABASE TENANCY COMMANDS (Phase 1)
// =========================================================================

Artisan::command('tenants:list', function () {
    $tenants = \App\Models\Tenant::with('domains')->orderBy('id')->get();
    if ($tenants->isEmpty()) {
        $this->warn('No tenants found in core_tenants.');
        return;
    }

    $rows = $tenants->map(fn (\App\Models\Tenant $t) => [
        $t->id,
        $t->name,
        $t->theme_module,
        $t->subdomain,
        $t->db_name ?? 'N/A',
        $t->status,
        $t->is_demo ? 'Yes' : 'No',
        $t->domains->pluck('domain')->implode(', '),
    ])->toArray();

    $this->table(['ID', 'Name', 'Theme', 'Subdomain', 'Database', 'Status', 'Demo', 'Domains'], $rows);
})->purpose('List all SaaS tenants and their isolated databases');

Artisan::command('tenants:create {name} {theme} {subdomain} {--demo} {--status=active}', function (string $name, string $theme, string $subdomain) {
    $modulePath = base_path("Modules/{$theme}");
    if (!File::isDirectory($modulePath)) {
        $this->error("Theme module [{$theme}] does not exist in Modules/ directory!");
        return 1;
    }

    $manager = app(\App\Services\TenantDatabaseManager::class);
    $isDemo = (bool) $this->option('demo');
    $status = (string) $this->option('status');

    $tenant = \App\Models\Tenant::create([
        'theme_module' => $theme,
        'name' => $name,
        'subdomain' => strtolower($subdomain),
        'status' => 'pending_verification',
        'is_demo' => $isDemo,
        'email_verified_at' => now(),
    ]);

    $this->info("Provisioning tenant #{$tenant->id} [{$tenant->name}] with isolated database...");
    $tenant = $manager->provisionTenant($tenant, $isDemo, $status);

    $this->info("Tenant #{$tenant->id} provisioned successfully! Database: [{$tenant->db_name}], Status: [{$tenant->status}]");
})->purpose('Create and provision a new SaaS tenant with its own isolated database');

Artisan::command('tenants:migrate {--tenant=} {--module=}', function () {
    $manager = app(\App\Services\TenantDatabaseManager::class);
    $query = \App\Models\Tenant::query()->whereNotNull('db_name');

    if ($tenantId = $this->option('tenant')) {
        $query->where('id', $tenantId);
    }
    if ($mod = $this->option('module')) {
        $query->where('theme_module', $mod);
    }

    $tenants = $query->get();
    if ($tenants->isEmpty()) {
        $this->warn('No matching tenants found to migrate.');
        return;
    }

    foreach ($tenants as $tenant) {
        $targetModule = $this->option('module') ?: $tenant->theme_module;
        $this->line("Migrating tenant #{$tenant->id} ({$tenant->db_name}) for module [{$targetModule}]...");
        try {
            $manager->migrateModule($tenant, $targetModule);
            $this->info("  ✓ Success ({$tenant->db_name})");
        } catch (\Throwable $e) {
            $this->error("  ✗ Failed ({$tenant->db_name}): " . $e->getMessage());
        }
    }

    $manager->disconnect();
})->purpose('Run module migrations across all or selected tenant databases');

Artisan::command('tenants:seed {--tenant=}', function () {
    $manager = app(\App\Services\TenantDatabaseManager::class);
    $query = \App\Models\Tenant::query()->whereNotNull('db_name');

    if ($tenantId = $this->option('tenant')) {
        $query->where('id', $tenantId);
    }

    foreach ($query->get() as $tenant) {
        $this->line("Seeding demo data for tenant #{$tenant->id} ({$tenant->db_name}) [{$tenant->theme_module}]...");
        $manager->seedModuleDemoData($tenant);
        $this->info("  ✓ Seeded ({$tenant->db_name})");
    }

    $manager->disconnect();
})->purpose('Seed demo data for tenant databases');

Artisan::command('tenants:sync-demos {--module=}', function () {
    $manager = app(\App\Services\TenantDatabaseManager::class);

    // 1. Sync core_websites into core_themes + demo tenants
    $websites = \App\Models\Website::whereNotNull('module_name')->orderBy('id')->get();
    if ($websites->isEmpty()) {
        foreach (\App\Support\ModuleCmsResolver::SUPPORTED_MODULES as $modKey => $meta) {
            \App\Models\Website::firstOrCreate(
                ['module_name' => $modKey],
                [
                    'name' => $meta['label'] . ' Demo',
                    'domain' => $meta['slug'] . '.multi-website-hub.test',
                    'slug' => $meta['slug'],
                    'module' => $meta['label'],
                    'module_badge' => $meta['badge'],
                    'status' => 'Published',
                    'is_active' => true,
                ]
            );
        }
        $websites = \App\Models\Website::whereNotNull('module_name')->orderBy('id')->get();
    }

    $filterMod = $this->option('module');

    foreach ($websites as $idx => $site) {
        $modName = $site->module_name;
        if (!File::isDirectory(base_path("Modules/{$modName}"))) {
            continue;
        }

        \App\Models\Theme::updateOrCreate(
            ['module_name' => $modName],
            [
                'name' => $site->module ?: $modName,
                'slug' => $site->slug ?: strtolower($modName),
                'badge_color' => $site->module_badge ?: 'purple',
                'thumbnail' => $site->thumbnail,
                'demo_url' => url('/' . ($site->slug ?: strtolower($modName))),
                'description' => $site->description,
                'is_public' => true,
                'is_active' => (bool) $site->is_active,
                'sort_order' => $idx + 1,
            ]
        );

        if ($filterMod && strcasecmp($filterMod, $modName) !== 0) {
            continue;
        }

        $subdomain = ($site->slug ?: strtolower($modName));
        $demoTenant = \App\Models\Tenant::firstOrCreate(
            ['theme_module' => $modName, 'is_demo' => true],
            [
                'name' => $site->name ?: ($modName . ' Live Demo'),
                'subdomain' => $subdomain,
                'status' => 'pending_verification',
                'email_verified_at' => now(),
            ]
        );

        if (!$demoTenant->db_name || $demoTenant->status !== 'active') {
            $this->line("Provisioning demo tenant for [{$modName}] -> Subdomain: [{$subdomain}]...");
            $demoTenant = $manager->provisionTenant($demoTenant, true, 'active');
            $this->info("  ✓ Demo tenant #{$demoTenant->id} ready in database [{$demoTenant->db_name}]");
        }
    }

    $manager->disconnect();
    $this->info('Themes and demo tenant databases synchronized!');
})->purpose('Sync core_websites into core_themes and provision isolated demo tenant databases');

Artisan::command('themes:link', function () {
    $themesPublicDir = public_path('themes');
    File::ensureDirectoryExists($themesPublicDir);

    foreach (File::directories(base_path('Modules')) as $modDir) {
        $modName = basename($modDir);
        $modLower = strtolower($modName);
        $targetAssets = $modDir . '/Public/assets';
        File::ensureDirectoryExists($targetAssets);

        $linkPath = $themesPublicDir . '/' . $modLower;
        if (is_link($linkPath) || File::exists($linkPath)) {
            continue;
        }

        try {
            app('files')->link($targetAssets, $linkPath);
            $this->line("Linked public/themes/{$modLower} -> Modules/{$modName}/Public/assets");
        } catch (\Throwable $e) {
            $this->warn("Could not symlink {$modLower} (fallback route/copy supported): " . $e->getMessage());
        }
    }

    $this->info('Per-module theme asset links checked!');
})->purpose('Create per-module asset symlinks under public/themes/{module}');

Artisan::command('saas:deploy {--sync-demos}', function () {
    $this->info('Starting SaaS VPS Deployment Pipeline...');

    // 1. Run Central Database Migrations
    $this->line('1. Running Central Database Migrations...');
    Artisan::call('migrate', ['--force' => true]);
    $this->info(trim(Artisan::output()) ?: '  ✓ Central migrations up to date.');

    // 2. Link Per-Module Theme Assets (Linux/Windows compatible)
    $this->line('2. Linking Theme Assets (public/themes/*)...');
    Artisan::call('themes:link');
    $this->info('  ✓ Theme assets linked.');

    // 3. Optionally sync demo tenants or migrate all existing tenant DBs
    if ($this->option('sync-demos')) {
        $this->line('3. Synchronizing Demo Tenant Databases...');
        Artisan::call('tenants:sync-demos');
    } else {
        $this->line('3. Running Module Migrations across existing Tenant Databases...');
        Artisan::call('tenants:migrate');
    }

    // 4. Clear & optimize caches
    $this->line('4. Clearing application caches...');
    Artisan::call('optimize:clear');

    $this->info('SaaS Deployment Completed Successfully!');
})->purpose('Deploy SaaS changes on VPS after git pull (central migrate, theme links, tenant DB migrate)');

Artisan::command('billing:check-expirations', function () {
    $billing = app(\App\Services\Billing\BillingService::class);
    $stats = $billing->processExpirations();
    $this->info("Billing check complete! Expired trials: {$stats['expired_trials']}, Suspended tenants: {$stats['suspended_tenants']}");
})->purpose('Check expired free trials and past-due subscriptions and auto-suspend tenants after grace period');

Schedule::command('billing:check-expirations')->daily();

Artisan::command('resellers:release-commissions', function () {
    $service = app(\App\Services\ResellerService::class);
    $count = $service->releaseMaturedCommissions();
    $this->info("Released {$count} matured reseller commission(s) into wallets!");
})->purpose('Release matured pending reseller commissions into reseller wallets');

Schedule::command('resellers:release-commissions')->daily();

