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
     * when resetting a module to its original default state.
     */
    public function clearModuleUploads(string $module): void
    {
        $uploadsDir = base_path("Modules/{$module}/Public/uploads");
        if (File::isDirectory($uploadsDir)) {
            foreach (File::directories($uploadsDir) as $subDir) {
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
