<?php

namespace App\Support;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleCmsResolver
{
    public const SUPPORTED_MODULES = [
        'Interior' => [
            'label' => 'Interior Design',
            'prefix' => 'int_',
            'slug' => 'interior-design',
            'badge' => 'purple',
        ],
        'RealEstate' => [
            'label' => 'Real Estate',
            'prefix' => 're_',
            'slug' => 'real-estate',
            'badge' => 'pink',
        ],
        'Pharmacy' => [
            'label' => 'Pharmacy',
            'prefix' => 'pharma_',
            'slug' => 'pharmacy',
            'badge' => 'green',
        ],
        'Clinic' => [
            'label' => 'Fertility Clinic',
            'prefix' => 'clinic_',
            'slug' => 'fertility-clinic',
            'badge' => 'green',
        ],
        'Portfolio' => [
            'label' => 'Developer Portfolio',
            'prefix' => 'port_',
            'slug' => 'developer-portfolio',
            'badge' => 'blue',
        ],
    ];

    /**
     * Resolve the currently active/selected module for Admin CMS management.
     */
    public static function currentModule(?Request $request = null): string
    {
        $request = $request ?? request();

        $activeWebsites = Website::activeModules();
        $activeModNames = $activeWebsites->pluck('module_name')->filter()->values()->all();

        // 1. Explicit query or input switch
        $requested = $request->input('cms_module');
        if ($requested && isset(self::SUPPORTED_MODULES[$requested])) {
            if (empty($activeModNames) || in_array($requested, $activeModNames, true)) {
                session(['admin_cms_module' => $requested]);
                return $requested;
            }
        }

        // 1b. Route-based automatic switch when visiting a module's own admin routes
        if ($request->routeIs('admin.interior.*')) {
            session(['admin_cms_module' => 'Interior']);
            return 'Interior';
        }
        if ($request->routeIs('admin.realestate.*')) {
            session(['admin_cms_module' => 'RealEstate']);
            return 'RealEstate';
        }
        if ($request->routeIs('admin.pharmacy.*')) {
            session(['admin_cms_module' => 'Pharmacy']);
            return 'Pharmacy';
        }
        if ($request->routeIs('admin.portfolio.*')) {
            session(['admin_cms_module' => 'Portfolio']);
            return 'Portfolio';
        }

        // 2. If ONLY 1 module is active in Manage Websites, always lock to that 1 module
        if (count($activeModNames) === 1) {
            $single = $activeModNames[0];
            session(['admin_cms_module' => $single]);
            return $single;
        }

        // 3. Check session if still active
        $sessionMod = session('admin_cms_module');
        if ($sessionMod && in_array($sessionMod, $activeModNames, true)) {
            return $sessionMod;
        }

        // 4. Default to first active module, or config fallback
        $fallback = $activeModNames[0] ?? config('app.active_module', 'Interior');
        if (!isset(self::SUPPORTED_MODULES[$fallback])) {
            $fallback = 'Interior';
        }

        session(['admin_cms_module' => $fallback]);
        return $fallback;
    }

    public static function moduleMeta(?string $module = null): array
    {
        $mod = $module ?? self::currentModule();
        return array_merge(
            ['key' => $mod],
            self::SUPPORTED_MODULES[$mod] ?? self::SUPPORTED_MODULES['Interior']
        );
    }

    public static function pageModel(?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        return "Modules\\{$mod}\\App\\Models\\ModulePage";
    }

    public static function menuModel(?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        return "Modules\\{$mod}\\App\\Models\\ModuleMenu";
    }

    public static function sliderModel(?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        return "Modules\\{$mod}\\App\\Models\\ModuleSlider";
    }

    public static function mediaModel(?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        return "Modules\\{$mod}\\App\\Models\\ModuleMedia";
    }

    public static function videoModel(?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        return "Modules\\{$mod}\\App\\Models\\ModuleVideo";
    }

    public static function themeModel(?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        return "Modules\\{$mod}\\App\\Models\\ModuleTheme";
    }

    public static function userModel(?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        return "Modules\\{$mod}\\App\\Models\\ModuleUser";
    }

    public static function settingModel(?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        return "Modules\\{$mod}\\App\\Models\\ModuleSetting";
    }

    /**
     * Store an uploaded file inside Modules/{Module}/Public/uploads/{folder}/
     * and return its public URL (/module-media/{Module}/{folder}/{filename}).
     */
    public static function storeModuleFile(UploadedFile $file, string $folder = 'media', ?string $module = null): string
    {
        $mod = $module ?? self::currentModule();
        $targetDir = base_path("Modules/{$mod}/Public/uploads/{$folder}");
        File::ensureDirectoryExists($targetDir);

        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '-' . Str::random(4) . '.' . $ext;
        $file->move($targetDir, $filename);

        return url("/module-media/{$mod}/{$folder}/{$filename}");
    }
}
