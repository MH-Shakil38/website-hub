<?php

namespace Modules\EventManagement\App\Models;

class ModulePage extends EventManagementBaseModel
{
    protected $table = 'em_pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'views',
    ];
}
