<?php

namespace Modules\Portfolio\App\Models;

class PortfolioEducation extends PortfolioBaseModel
{
    protected $table = 'educations';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
