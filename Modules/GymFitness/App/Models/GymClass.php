<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymClass extends GymFitnessBaseModel
{
    protected $table = 'classes';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(GymTrainer::class, 'trainer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(GymClassBooking::class, 'class_id');
    }
}
