<?php

namespace Modules\Portfolio\App\Models;

class PortfolioTestimonial extends PortfolioBaseModel
{
    protected $table = 'testimonials';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
