<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GymMember extends GymFitnessBaseModel
{
    protected $table = 'members';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'join_date' => 'date',
            'expiry_date' => 'date',
            'due_amount' => 'decimal:2',
        ];
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(GymPackage::class, 'package_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(GymTrainer::class, 'trainer_id');
    }

    public function workoutPlan(): BelongsTo
    {
        return $this->belongsTo(GymFitnessPlan::class, 'workout_plan_id');
    }

    public function dietPlan(): BelongsTo
    {
        return $this->belongsTo(GymFitnessPlan::class, 'diet_plan_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(GymSubscription::class, 'member_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(GymPayment::class, 'member_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(GymAttendance::class, 'member_id');
    }

    public function classBookings(): HasMany
    {
        return $this->hasMany(GymClassBooking::class, 'member_id');
    }

    public function progressLogs(): HasMany
    {
        return $this->hasMany(GymProgressLog::class, 'member_id');
    }
}
