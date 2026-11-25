<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymSubscription extends GymFitnessBaseModel
{
    protected $table = 'subscriptions';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'amount' => 'decimal:2',
            'registration_fee' => 'decimal:2',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(GymMember::class, 'member_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(GymPackage::class, 'package_id');
    }
}
