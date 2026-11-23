<?php

namespace Modules\WonderLearn\App\Models;

class WlStory extends WonderLearnBaseModel
{
    protected $table = 'wl_stories';

    protected $fillable = [
        'title',
        'slug',
        'category_tag',
        'tag_bg',
        'tag_color',
        'image',
        'summary',
        'story_content',
        'reading_time',
        'age_range',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }
}
