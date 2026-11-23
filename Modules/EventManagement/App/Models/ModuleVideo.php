<?php

namespace Modules\EventManagement\App\Models;

class ModuleVideo extends EventManagementBaseModel
{
    protected $table = 'em_videos';

    protected $fillable = [
        'title',
        'description',
        'category',
        'video_url',
        'thumbnail',
        'duration',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
