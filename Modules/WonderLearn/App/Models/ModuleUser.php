<?php

namespace Modules\WonderLearn\App\Models;

class ModuleUser extends WonderLearnBaseModel
{
    protected $table = 'wl_users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_name',
        'status',
        'avatar',
    ];
}
