<?php

namespace Modules\HajjUmrah\App\Models;

class HajjBlog extends HajjUmrahBaseModel
{
    protected $table = 'blogs';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }
}
