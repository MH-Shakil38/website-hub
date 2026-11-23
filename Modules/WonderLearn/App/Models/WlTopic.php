<?php

namespace Modules\WonderLearn\App\Models;

class WlTopic extends WonderLearnBaseModel
{
    protected $table = 'wl_topics';

    protected $fillable = [
        'title',
        'slug',
        'card_bg',
        'border_color',
        'image',
        'description',
        'age_group',
        'activities_count',
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
