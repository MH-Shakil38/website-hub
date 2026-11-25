<?php

namespace Modules\GymFitness\App\Models;

class GymFaq extends GymFitnessBaseModel
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
