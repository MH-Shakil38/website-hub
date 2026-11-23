<?php

namespace Modules\WonderLearn\App\Models;

class ModuleVideo extends WonderLearnBaseModel
{
    protected $table = 'wl_videos';

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
