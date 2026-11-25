<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Reseller extends Model
{
    protected $connection = 'central';

    protected $table = 'core_resellers';

    protected $fillable = [
        'user_id',
        'plan_id',
        'company_name',
        'phone',
        'kyc_document_type',
        'kyc_document_number',
        'kyc_status',
        'referral_code',
        'referral_clicks',
        'billing_mode',
        'commission_rate',
        'site_quota',
        'sites_used',
        'wallet_balance',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'wallet_balance' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $reseller) {
            if (empty($reseller->referral_code)) {
                $reseller->referral_code = 'RES' . strtoupper(Str::random(5));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'reseller_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'reseller_id');
    }

    public function payoutRequests(): HasMany
    {
        return $this->hasMany(PayoutRequest::class, 'reseller_id');
    }

    public function referralUrl(): string
    {
        return url('/?ref=' . $this->referral_code);
    }
}
