<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleStorageService
{
    /**
     * Store an uploaded file inside Modules/{Module}/Public/uploads/{folder}/
     * and return its public URL (/module-media/{Module}/{folder}/{filename}).
     */
    public function store(UploadedFile $file, string $folder = 'media', string $module = 'Interior'): string
    {
        return \App\Support\SafeUpload::store($file, $folder, $module);
    }

    /**
     * Clear all dynamically uploaded files inside Modules/{Module}/Public/uploads/
     * when resetting a module or a specific tenant to its original default state.
     */
    public function clearModuleUploads(string $module, ?int $tenantId = null): void
    {
        if ($tenantId === null && app()->bound('tenant') && config('app.mode', 'saas') === 'saas') {
            $tenantId = app('tenant')->id;
        }

        if ($tenantId !== null) {
            $tenantDir = base_path("Modules/{$module}/Public/uploads/t{$tenantId}");
            if (File::isDirectory($tenantDir)) {
                File::deleteDirectory($tenantDir);
                File::ensureDirectoryExists($tenantDir);
            }
            return;
        }

        $uploadsDir = base_path("Modules/{$module}/Public/uploads");
        if (File::isDirectory($uploadsDir)) {
            foreach (File::directories($uploadsDir) as $subDir) {
                $base = basename($subDir);
                if ($base === 'theme' || preg_match('/^t\d+$/', $base)) {
                    continue;
                }
                File::deleteDirectory($subDir);
            }
            foreach (File::files($uploadsDir) as $file) {
                if ($file->getFilename() !== '.gitkeep') {
                    File::delete($file->getPathname());
                }
            }
        }
    }
}
