<?php

namespace Modules\EventManagement\App\Models;

class EmSpeaker extends EventManagementBaseModel
{
    protected $table = 'em_speakers';

    protected $fillable = [
        'name',
        'slug',
        'designation',
        'company',
        'avatar',
        'bio',
        'linkedin_url',
        'twitter_url',
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
