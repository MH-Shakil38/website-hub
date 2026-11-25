<?php

namespace Modules\GymFitness\App\Models;

class ModuleTheme extends GymFitnessBaseModel
{
    protected $table = 'themes';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
