<?php

namespace Modules\WonderLearn\App\Models;

class WlAdventure extends WonderLearnBaseModel
{
    protected $table = 'wl_adventures';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'button_text',
        'button_url',
        'banner_image',
        'badge_text',
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
