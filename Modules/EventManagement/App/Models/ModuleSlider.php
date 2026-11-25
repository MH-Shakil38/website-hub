<?php

namespace Modules\EventManagement\App\Models;

class ModuleSlider extends EventManagementBaseModel
{
    protected $table = 'em_sliders';

    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
        'button_url',
        'image',
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
