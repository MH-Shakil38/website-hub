<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Website;
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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
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

                $migPath = $modPath . '/Database/Migrations';
                if (File::isDirectory($migPath)) {
                    $this->loadMigrationsFrom($migPath);
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
