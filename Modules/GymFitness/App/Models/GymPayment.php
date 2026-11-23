<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymPayment extends GymFitnessBaseModel
{
    protected $table = 'payments';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'paid_at' => 'datetime',
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
