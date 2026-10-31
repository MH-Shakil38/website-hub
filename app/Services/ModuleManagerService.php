<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Website;
use App\Support\ModuleCmsResolver;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ModuleManagerService
{
    public function __construct(
        protected ModuleStorageService $storageService
    ) {}

    /**
     * Reset a specific module (e.g., Interior, RealEstate, Pharmacy) back to 100% original default state.
     * Does NOT touch any other module!
     */
    public function resetModuleToDefault(string $module): void
    {
        if (!isset(ModuleCmsResolver::SUPPORTED_MODULES[$module])) {
            return;
        }

        $meta = ModuleCmsResolver::moduleMeta($module);
        $prefix = $meta['prefix'];

        // 1. Clear custom tables for this specific module so it falls back 100% to Config/defaults.php
        $tablesToClear = [
            $prefix . 'settings',
            $prefix . 'sliders',
            $prefix . 'menus',
            $prefix . 'projects',
            $prefix . 'skills',
            $prefix . 'services',
            $prefix . 'experiences',
            $prefix . 'educations',
            $prefix . 'testimonials',
            $prefix . 'blogs',
            $prefix . 'faqs',
            $prefix . 'media',
            $prefix . 'videos',
            $prefix . 'themes',
        ];

        foreach ($tablesToClear as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        if ($module === 'Portfolio' && class_exists(\Modules\Portfolio\App\Services\PortfolioDataService::class)) {
            app(\Modules\Portfolio\App\Services\PortfolioDataService::class)->ensureSeeded();
        }

        // 2. Remove custom uploaded files from Modules/{Module}/Public/uploads/
        $this->storageService->clearModuleUploads($module);

        // 3. Log activity
        ActivityLog::record(
            "Module reset to default ({$meta['label']})",
            "Restored 100% original default theme & assets for {$meta['label']}",
            'bi-arrow-counterclockwise',
            '#FFE4EC',
            '#EC134A'
        );

        Artisan::call('view:clear');
    }

    /**
     * Reset a Website record's linked module to default.
     */
    public function resetWebsiteToDefault(Website $website): string
    {
        $module = $website->module_name ?: 'Interior';
        $this->resetModuleToDefault($module);
        return $module;
    }
}
