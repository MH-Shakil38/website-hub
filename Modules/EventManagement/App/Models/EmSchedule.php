<?php

namespace Modules\EventManagement\App\Models;

class EmSchedule extends EventManagementBaseModel
{
    protected $table = 'em_schedules';

    protected $fillable = [
        'day_key',
        'day_label',
        'day_date',
        'session_number',
        'title',
        'time_range',
        'venue_location',
        'image',
        'speaker1_name',
        'speaker1_role',
        'speaker1_avatar',
        'speaker2_name',
        'speaker2_role',
        'speaker2_avatar',
        'button_text',
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
