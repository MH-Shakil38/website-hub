<?php

namespace Modules\GymFitness\App\Models;

class ModuleVideo extends GymFitnessBaseModel
{
    protected $table = 'videos';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
