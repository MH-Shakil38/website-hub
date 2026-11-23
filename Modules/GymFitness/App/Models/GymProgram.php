<?php

namespace Modules\GymFitness\App\Models;

class GymProgram extends GymFitnessBaseModel
{
    protected $table = 'programs';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
