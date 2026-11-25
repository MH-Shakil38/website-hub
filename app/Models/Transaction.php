<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $connection = 'central';

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

    /** Money received (Payments) minus money already paid out (Settlements). */
    public static function availableBalance(): float
    {
        try {
            $row = static::query()
                ->selectRaw("COALESCE(SUM(CASE WHEN type = 'Settlement' THEN 0 ELSE net_amount END), 0) as received")
                ->selectRaw("COALESCE(SUM(CASE WHEN type = 'Settlement' THEN net_amount ELSE 0 END), 0) as settled")
                ->first();

            return (float) $row->received - (float) $row->settled;
        } catch (\Throwable) {
            return 0.0;
        }
    }
}
