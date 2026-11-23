<?php

namespace Modules\WonderLearn\App\Models;

class ModuleMenu extends WonderLearnBaseModel
{
    protected $table = 'wl_menus';

    protected $fillable = [
        'title',
        'url',
        'location',
        'target',
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
