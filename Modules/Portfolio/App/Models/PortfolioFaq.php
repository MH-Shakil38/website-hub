<?php

namespace Modules\Portfolio\App\Models;

class PortfolioFaq extends PortfolioBaseModel
{
    protected $table = 'faqs';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
