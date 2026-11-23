<?php

namespace Modules\EventManagement\App\Models;

class EmPricingPlan extends EventManagementBaseModel
{
    protected $table = 'em_pricing_plans';

    protected $fillable = [
        'name',
        'slug',
        'price',
        'currency',
        'subtitle',
        'features_text',
        'button_text',
        'is_highlighted',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_highlighted' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
