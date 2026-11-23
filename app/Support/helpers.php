<?php

namespace App\Support;

if (!function_exists('theme_asset')) {
    /**
     * Generate a public URL for a theme module's static asset.
     */
    function theme_asset(string $path, ?string $module = null): string
    {
        if ($module === null) {
            $module = app()->bound('tenant')
                ? app('tenant')->theme_module
                : config('app.active_module', 'Interior');
        }

        $modLower = strtolower($module);
        return asset('themes/' . $modLower . '/' . ltrim($path, '/'));
    }
}

if (!function_exists('current_tenant')) {
    /**
     * Get the currently connected SaaS tenant instance, if any.
     */
    function current_tenant(): ?\App\Models\Tenant
    {
        return app()->bound('tenant') ? app('tenant') : null;
    }
}
