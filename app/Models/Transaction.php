<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'core_transactions';

    protected $fillable = [
        'transacted_at',
        'wallet_number',
        'amount',
        'coupon_amount',
        'net_amount',
        'cashback_amount',
        'type',
        'channel',
        'transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'transacted_at' => 'datetime',
            'amount' => 'decimal:2',
            'coupon_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'cashback_amount' => 'decimal:2',
        ];
    }
}
