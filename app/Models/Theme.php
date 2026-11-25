<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Theme extends Model
{
    protected $connection = 'central';

    protected $table = 'core_themes';

    protected $fillable = [
        'module_name',
        'name',
        'slug',
        'category',
        'badge_color',
        'thumbnail',
        'demo_url',
        'description',
        'is_public',
        'is_active',
        'allowed_plans',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'is_active' => 'boolean',
            'allowed_plans' => 'array',
        ];
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'theme_module', 'module_name');
    }
}
