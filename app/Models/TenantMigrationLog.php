<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantMigrationLog extends Model
{
    protected $connection = 'central';

    protected $table = 'core_tenant_migrations_log';

    protected $fillable = [
        'tenant_id',
        'module',
        'migration',
        'status',
        'error',
        'ran_at',
    ];

    protected function casts(): array
    {
        return [
            'ran_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
