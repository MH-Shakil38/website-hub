<?php

namespace Modules\WonderLearn\App\Models;

class WlTestimonial extends WonderLearnBaseModel
{
    protected $table = 'wl_testimonials';

    protected $fillable = [
        'parent_name',
        'child_age_label',
        'quote',
        'rating',
        'avatar',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
