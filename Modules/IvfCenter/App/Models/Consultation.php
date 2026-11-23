<?php

namespace Modules\IvfCenter\App\Models;

class Consultation extends IvfCenterBaseModel
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'service_interest',
        'preferred_date',
        'message',
        'status',
    ];
}
