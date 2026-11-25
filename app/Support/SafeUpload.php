<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Single place that decides which uploaded files may be stored in
 * Modules/{Module}/Public/uploads and which Content-Type they are served with.
 * The extension is derived from the file CONTENT (not the client-supplied name),
 * and only non-scriptable formats are allowed (no html, svg, js, php, ...).
 */
class SafeUpload
{
    public const MIME_BY_EXT = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'avif' => 'image/avif',
        'ico' => 'image/x-icon',
        'pdf' => 'application/pdf',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'ogg' => 'video/ogg',
    ];

    public static function store(UploadedFile $file, string $folder, string $module): string
    {
        $ext = strtolower((string) $file->guessExtension());
        if ($ext === 'jpe') {
            $ext = 'jpg';
        }
        if ($ext === 'oga' || $ext === 'ogv') {
            $ext = 'ogg';
        }

        if (!isset(self::MIME_BY_EXT[$ext])) {
            throw ValidationException::withMessages([
                'file' => 'This file type is not allowed. Allowed: JPG, PNG, GIF, WEBP, AVIF, ICO, PDF, MP4, WEBM, OGG.',
            ]);
        }

        $safeModule = basename($module);
        $safeFolder = preg_replace('/[^A-Za-z0-9_\-]/', '', $folder) ?: 'media';
        // SaaS mode: each tenant gets its own upload folder (uploads/t{id}/{folder}).
        $tenant = app()->bound('tenant') ? app('tenant') : null;
        $tenantPrefix = ($tenant && config('app.mode', 'saas') === 'saas') ? "t{$tenant->id}/" : '';

        $targetDir = base_path("Modules/{$safeModule}/Public/uploads/{$tenantPrefix}{$safeFolder}");
        File::ensureDirectoryExists($targetDir);

        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';
        $filename = $baseName . '-' . time() . '-' . Str::random(6) . '.' . $ext;

        $file->move($targetDir, $filename);

        if ($tenantPrefix !== '') {
            return url("/module-media/{$safeModule}/t{$tenant->id}/{$safeFolder}/{$filename}");
        }

        return url("/module-media/{$safeModule}/{$safeFolder}/{$filename}");
    }

    public static function mimeForFilename(string $filename): ?string
    {
        return self::MIME_BY_EXT[strtolower(pathinfo($filename, PATHINFO_EXTENSION))] ?? null;
    }
}
