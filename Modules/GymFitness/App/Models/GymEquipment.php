<?php

namespace Modules\GymFitness\App\Models;

class GymEquipment extends GymFitnessBaseModel
{
    protected $table = 'equipments';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'last_maintenance_date' => 'date',
            'next_maintenance_date' => 'date',
            'is_active' => 'boolean',
        ];
    }
}
