<?php

namespace Modules\Portfolio\App\Models;

class PortfolioSkill extends PortfolioBaseModel
{
    protected $table = 'skills';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
