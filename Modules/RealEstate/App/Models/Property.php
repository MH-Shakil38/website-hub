<?php

namespace Modules\RealEstate\App\Models;

class Property extends RealEstateBaseModel
{
    protected $table = 're_properties';

    protected $fillable = [
        'title',
        'slug',
        'location',
        'price',
        'image',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }
}
