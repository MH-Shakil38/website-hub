<?php

namespace Modules\Pharmacy\App\Models;

class Medicine extends PharmacyBaseModel
{
    protected $table = 'pharma_medicines';

    protected $fillable = [
        'name',
        'slug',
        'generic_name',
        'price',
        'stock',
        'image',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }
}
