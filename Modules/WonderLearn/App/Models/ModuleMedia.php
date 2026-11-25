<?php

namespace Modules\WonderLearn\App\Models;

class ModuleMedia extends WonderLearnBaseModel
{
    protected $table = 'wl_media';

    protected $fillable = [
        'title',
        'category',
        'file_path',
        'file_type',
        'file_size',
        'alt_text',
    ];
}
