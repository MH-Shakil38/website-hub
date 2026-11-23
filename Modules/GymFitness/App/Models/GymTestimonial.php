<?php

namespace Modules\GymFitness\App\Models;

class GymTestimonial extends GymFitnessBaseModel
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
