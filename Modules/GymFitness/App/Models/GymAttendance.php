<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymAttendance extends GymFitnessBaseModel
{
    protected $table = 'attendances';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(GymMember::class, 'member_id');
    }
}
