<?php

namespace Modules\Portfolio\App\Models;

class PortfolioBlog extends PortfolioBaseModel
{
    protected $table = 'blogs';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
