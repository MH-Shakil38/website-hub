<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymClassBooking extends GymFitnessBaseModel
{
    protected $table = 'class_bookings';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
        ];
    }

    public function gymClass(): BelongsTo
    {
        return $this->belongsTo(GymClass::class, 'class_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(GymMember::class, 'member_id');
    }
}
