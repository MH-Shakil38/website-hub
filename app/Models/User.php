<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $connection = 'central';

    protected $fillable = [
        'role_id',
        'type',
        'reseller_id',
        'referred_via',
        'name',
        'email',
        'phone',
        'avatar',
        'status',
        'password',
        'two_factor_enabled',
        'two_factor_code',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function reseller(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reseller_id');
    }

    public function clients(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(self::class, 'reseller_id');
    }

    public function tenants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tenant::class, 'owner_id');
    }

    public function subscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Subscription::class, 'user_id');
    }

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class, 'user_id');
    }

    /**
     * Get the user's currently active or trial subscription (with its Plan).
     */
    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->with('plan')
            ->whereIn('status', ['trial', 'active'])
            ->latest('id')
            ->first();
    }

    /**
     * Get the effective Plan for this user (or null if none).
     */
    public function currentPlan(): ?Plan
    {
        return $this->activeSubscription()?->plan;
    }

    /**
     * Maximum number of websites (tenants) this user is allowed to create under their current plan.
     */
    public function allowedSiteLimit(): int
    {
        if ($this->isSuperAdmin()) {
            return 999999;
        }

        $plan = $this->currentPlan();
        return $plan ? (int) $plan->site_limit : 1;
    }

    /**
     * Check whether the user's current plan allows custom domain connection.
     */
    public function canUseCustomDomain(): bool
    {
        if ($this->isSuperAdmin() || $this->isReseller()) {
            return true;
        }

        $plan = $this->currentPlan();
        return $plan ? (bool) $plan->custom_domain : true;
    }

    public function isSuperAdmin(): bool
    {
        return $this->type === 'super_admin' || ($this->role && $this->role->slug === 'administrator' && empty($this->type));
    }

    public function isReseller(): bool
    {
        return $this->type === 'reseller';
    }

    public function isClient(): bool
    {
        return $this->type === 'client' || !$this->isSuperAdmin() && !$this->isReseller();
    }

    public function panelType(): string
    {
        if ($this->isSuperAdmin()) {
            return 'super_admin';
        }
        if ($this->isReseller()) {
            return 'reseller';
        }

        return 'client';
    }

    /**
     * Query builder for Tenants accessible to this user depending on role (Super Admin, Reseller, or Client).
     */
    public function accessibleTenantsQuery(): \Illuminate\Database\Eloquent\Builder
    {
        if ($this->isSuperAdmin()) {
            return Tenant::query();
        }

        if ($this->isReseller()) {
            $clientIds = $this->clients()->pluck('id')->all();
            return Tenant::query()->where(function ($q) use ($clientIds) {
                $q->where('reseller_id', $this->id)
                    ->orWhere('owner_id', $this->id);
                if (!empty($clientIds)) {
                    $q->orWhereIn('owner_id', $clientIds);
                }
            });
        }

        return Tenant::query()->where('owner_id', $this->id);
    }

    public function canAccessTenant(Tenant $tenant): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->isReseller()) {
            return (int) $tenant->reseller_id === (int) $this->id
                || (int) $tenant->owner_id === (int) $this->id
                || ($tenant->owner && (int) $tenant->owner->reseller_id === (int) $this->id);
        }

        return (int) $tenant->owner_id === (int) $this->id;
    }

    public function hasPermission(string $permissionSlug): bool
    {
        if (!$this->role) {
            return false;
        }

        if ($this->role->slug === 'administrator') {
            return true;
        }

        return $this->role->permissions()->where('slug', $permissionSlug)->exists();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && str_starts_with($this->avatar, 'http')) {
            return $this->avatar;
        }

        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0F172A&color=fff&size=80';
    }
}
