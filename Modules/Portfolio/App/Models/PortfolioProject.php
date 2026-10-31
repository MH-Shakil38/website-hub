<?php

namespace Modules\Portfolio\App\Models;

class PortfolioProject extends PortfolioBaseModel
{
    protected $table = 'projects';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'technologies' => 'array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
