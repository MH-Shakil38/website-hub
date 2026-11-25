<?php

namespace Modules\WonderLearn\App\Models;

class ModuleTheme extends WonderLearnBaseModel
{
    protected $table = 'wl_themes';

    protected $fillable = [
        'name',
        'slug',
        'primary_color',
        'secondary_color',
        'font_family',
        'layout_style',
        'thumbnail',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
