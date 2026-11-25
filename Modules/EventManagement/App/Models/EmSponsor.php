<?php

namespace Modules\EventManagement\App\Models;

class EmSponsor extends EventManagementBaseModel
{
    protected $table = 'em_sponsors';

    protected $fillable = [
        'name',
        'icon_class',
        'logo',
        'website_url',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
