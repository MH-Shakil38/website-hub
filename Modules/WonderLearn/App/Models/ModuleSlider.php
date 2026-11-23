<?php

namespace Modules\WonderLearn\App\Models;

class ModuleSlider extends WonderLearnBaseModel
{
    protected $table = 'wl_sliders';

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
