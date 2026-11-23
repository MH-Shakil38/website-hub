<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymProgressLog extends GymFitnessBaseModel
{
    protected $table = 'progress_logs';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'recorded_date' => 'date',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'bmi' => 'decimal:2',
            'body_fat_pct' => 'decimal:2',
            'chest_cm' => 'decimal:2',
            'waist_cm' => 'decimal:2',
            'arms_cm' => 'decimal:2',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(GymMember::class, 'member_id');
    }
}
