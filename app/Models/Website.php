<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Website extends Model
{
    protected $table = 'core_websites';

    protected $fillable = [
        'module_name',
        'name',
        'domain',
        'slug',
        'thumbnail',
        'module',
        'module_badge',
        'admin_route',
        'description',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'website_id');
    }

    /**
     * Get the frontend URL for this website depending on whether 1 or multiple modules are active.
     */
    public function getFrontendUrlAttribute(): string
    {
        if (!$this->is_active) {
            return '#';
        }

        $activeModuleCount = static::activeModules()->count();

        if ($activeModuleCount === 1) {
            return url('/');
        }

        return url('/' . ltrim($this->slug ?: strtolower($this->module_name ?? 'site'), '/'));
    }

    /**
     * Scope / helper for active module websites that have a real module directory.
     */
    public static function activeModules()
    {
        try {
            if (!Schema::hasTable('core_websites') || !Schema::hasColumn('core_websites', 'is_active')) {
                return collect();
            }

            return static::where('is_active', true)
                ->whereNotNull('module_name')
                ->orderBy('id')
                ->get()
                ->filter(fn (self $site) => File::isDirectory(base_path('Modules/' . $site->module_name)))
                ->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Check if a specific module is active.
     */
    public static function isModuleActive(string $moduleName): bool
    {
        $active = static::activeModules();
        if ($active->isEmpty()) {
            return config('app.active_module') === $moduleName;
        }

        return $active->contains(fn (self $site) => strcasecmp($site->module_name, $moduleName) === 0);
    }

    /**
     * Synchronize modules_statuses.json and .env ACTIVE_MODULE with database state.
     */
    public static function syncModuleStatuses(): void
    {
        $allSites = static::whereNotNull('module_name')->get();
        $statusFile = base_path('modules_statuses.json');
        $statuses = File::exists($statusFile) ? (json_decode(File::get($statusFile), true) ?: []) : [];

        foreach ($allSites as $site) {
            if ($site->module_name && File::isDirectory(base_path('Modules/' . $site->module_name))) {
                $statuses[$site->module_name] = (bool) $site->is_active;
            }
        }

        File::put($statusFile, json_encode($statuses, JSON_PRETTY_PRINT));

        // Update ACTIVE_MODULE in .env to the first active module
        $firstActive = $allSites->firstWhere('is_active', true);
        if ($firstActive && $firstActive->module_name) {
            $module = $firstActive->module_name;
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

            // Ensure symlink for primary active module
            $linkPath = public_path('module-assets');
            $targetPath = base_path("Modules/{$module}/Public");
            File::ensureDirectoryExists($targetPath . '/assets');
            File::ensureDirectoryExists($targetPath . '/uploads');

            if (is_link($linkPath) || File::exists($linkPath)) {
                @unlink($linkPath);
                @rmdir($linkPath);
            }
            @app('files')->link($targetPath, $linkPath);
        }
    }
}
