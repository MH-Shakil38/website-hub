<?php

namespace Modules\Portfolio\App\Models;

class PortfolioExperience extends PortfolioBaseModel
{
    protected $table = 'experiences';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
