<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Website;
use App\Services\SaasMailService;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantDatabaseManager::class, fn () => new TenantDatabaseManager());
        $this->app->singleton(SaasMailService::class, fn () => new SaasMailService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dynamically apply Super Admin Email & SMTP settings stored in core_settings
        try {
            app(SaasMailService::class)->applyMailConfig();
        } catch (\Throwable) {
            // Fallback to default config/mail.php if database is not yet migrated
        }
        // Ensure all modules in Modules/ have their views and configs registered dynamically
        $modulesDir = base_path('Modules');
        if (File::isDirectory($modulesDir)) {
            foreach (File::directories($modulesDir) as $modPath) {
                $modName = basename($modPath);
                $modLower = strtolower($modName);

                $viewPath = $modPath . '/resources/views';
                if (File::isDirectory($viewPath)) {
                    $this->loadViewsFrom($viewPath, $modLower);
                }

                $cfgPath = $modPath . '/Config/config.php';
                if (File::exists($cfgPath)) {
                    $this->mergeConfigFrom($cfgPath, $modLower);
                }

                // Only auto-load module migrations on the default connection in legacy single-module mode.
                // In SaaS mode, module migrations run explicitly per tenant DB via TenantDatabaseManager.
                if (config('app.mode', 'saas') === 'single') {
                    $migPath = $modPath . '/Database/Migrations';
                    if (File::isDirectory($migPath)) {
                        $this->loadMigrationsFrom($migPath);
                    }
                }
            }
        }

        View::composer('*', function ($view) {
            static $sharedData = null;

            if ($sharedData === null) {
                $sharedData = [
                    'globalSiteName' => 'SiteBuilder',
                    'globalSiteTagline' => 'Website Management',
                    'globalSiteLogo' => null,
                    'globalCopyright' => '© 2025 SiteBuilder. All rights reserved.',
                    'activeModuleWebsites' => collect(),
                ];

                try {
                    if (Schema::hasTable('core_settings')) {
                        $all = Setting::pluck('value', 'key')->toArray();
                        $sharedData['globalSiteName'] = $all['site_name'] ?? 'SiteBuilder';
                        $sharedData['globalSiteTagline'] = $all['site_tagline'] ?? 'Website Management';
                        $sharedData['globalSiteLogo'] = !empty($all['site_logo']) ? asset('storage/' . $all['site_logo']) : null;
                        $sharedData['globalCopyright'] = $all['copyright_text'] ?? ('© ' . date('Y') . ' ' . ($all['site_name'] ?? 'SiteBuilder') . '. All rights reserved.');
                    }

                    if (Schema::hasTable('core_websites')) {
                        $sharedData['activeModuleWebsites'] = Website::activeModules();
                    }
                } catch (\Throwable $e) {
                    // Fallback if DB is not ready
                }
            }

            $view->with($sharedData);
        });
    }
}
