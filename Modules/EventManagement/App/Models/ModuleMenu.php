<?php

namespace Modules\EventManagement\App\Models;

class ModuleMenu extends EventManagementBaseModel
{
    protected $table = 'em_menus';

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
