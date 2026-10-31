<?php

namespace Modules\Portfolio\App\Models;

class PortfolioService extends PortfolioBaseModel
{
    protected $table = 'services';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
