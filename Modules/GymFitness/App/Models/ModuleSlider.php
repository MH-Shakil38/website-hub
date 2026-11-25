<?php

namespace Modules\GymFitness\App\Models;

class ModuleSlider extends GymFitnessBaseModel
{
    protected $table = 'sliders';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
