<?php

namespace Modules\GymFitness\App\Models;

class GymFitnessPlan extends GymFitnessBaseModel
{
    protected $table = 'fitness_plans';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
