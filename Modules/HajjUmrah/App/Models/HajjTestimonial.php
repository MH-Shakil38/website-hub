<?php

namespace Modules\HajjUmrah\App\Models;

class HajjTestimonial extends HajjUmrahBaseModel
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
