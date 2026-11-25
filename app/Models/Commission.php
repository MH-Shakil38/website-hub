<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    protected $connection = 'central';

    protected $table = 'core_commissions';

    protected $fillable = [
        'reseller_id',
        'client_id',
        'invoice_id',
        'invoice_amount',
        'rate',
        'amount',
        'status',
        'available_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'invoice_amount' => 'decimal:2',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
            'available_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Reseller::class, 'reseller_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
