<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class GymTrainer extends GymFitnessBaseModel
{
    protected $table = 'trainers';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(GymMember::class, 'trainer_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(GymClass::class, 'trainer_id');
    }
}
