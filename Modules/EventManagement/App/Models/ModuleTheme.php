<?php

namespace Modules\EventManagement\App\Models;

class ModuleTheme extends EventManagementBaseModel
{
    protected $table = 'em_themes';

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
