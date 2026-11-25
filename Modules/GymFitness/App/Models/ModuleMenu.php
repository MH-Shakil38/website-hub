<?php

namespace Modules\GymFitness\App\Models;

class ModuleMenu extends GymFitnessBaseModel
{
    protected $table = 'menus';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
