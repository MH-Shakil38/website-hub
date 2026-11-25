<?php

namespace Modules\EventManagement\App\Models;

class ModuleUser extends EventManagementBaseModel
{
    protected $table = 'em_users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_name',
        'status',
        'avatar',
    ];
}
