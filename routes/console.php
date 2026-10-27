<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

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
