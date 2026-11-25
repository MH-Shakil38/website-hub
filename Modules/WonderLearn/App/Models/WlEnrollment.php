<?php

namespace Modules\WonderLearn\App\Models;

class WlEnrollment extends WonderLearnBaseModel
{
    protected $table = 'wl_enrollments';

    protected $fillable = [
        'parent_name',
        'child_name',
        'child_age',
        'email',
        'phone',
        'favorite_topic',
        'plan_type',
        'status',
    ];
}
