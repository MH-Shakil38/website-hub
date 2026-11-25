<?php

namespace Modules\EventManagement\App\Models;

class ModuleMedia extends EventManagementBaseModel
{
    protected $table = 'em_media';

    protected $fillable = [
        'title',
        'category',
        'file_path',
        'file_type',
        'file_size',
        'alt_text',
    ];
}
