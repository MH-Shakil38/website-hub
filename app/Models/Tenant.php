<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use SoftDeletes;

    protected $connection = 'central';

    protected $table = 'core_tenants';

    protected $fillable = [
        'uuid',
        'owner_id',
        'reseller_id',
        'plan_id',
        'billing_cycle',
        'theme_module',
        'name',
        'subdomain',
        'status',
        'verification_token',
        'email_verified_at',
        'trial_ends_at',
        'storage_used_mb',
        'storage_limit_mb',
        'db_name',
        'db_host',
        'db_port',
        'db_username',
        'db_password',
        'is_demo',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'db_password' => 'encrypted',
            'is_demo' => 'boolean',
            'meta' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $tenant) {
            if (empty($tenant->uuid)) {
                $tenant->uuid = (string) Str::uuid();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class, 'theme_module', 'module_name');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class, 'tenant_id');
    }

    public function migrationLogs(): HasMany
    {
        return $this->hasMany(TenantMigrationLog::class, 'tenant_id');
    }

    public function isAccessible(): bool
    {
        return in_array($this->status, ['trial', 'active'], true);
    }

    public function generateDefaultDbName(): string
    {
        return sprintf('hub_t_%04d', $this->id);
    }
}
