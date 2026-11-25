<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    protected $connection = 'central';

    protected $table = 'core_subscriptions';

    protected $fillable = [
        'user_id',
        'tenant_id',
        'plan_id',
        'billing_cycle',
        'status',
        'starts_at',
        'ends_at',
        'grace_ends_at',
        'auto_renew',
        'last_reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'grace_ends_at' => 'datetime',
            'last_reminder_sent_at' => 'datetime',
            'auto_renew' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }

    public function isValid(): bool
    {
        if (!in_array($this->status, ['trial', 'active'], true)) {
            return false;
        }

        if ($this->ends_at && $this->ends_at->isPast()) {
            if ($this->grace_ends_at && $this->grace_ends_at->isFuture()) {
                return true;
            }
            return false;
        }

        return true;
    }
}
