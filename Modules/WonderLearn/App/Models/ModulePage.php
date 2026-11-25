<?php

namespace Modules\WonderLearn\App\Models;

class ModulePage extends WonderLearnBaseModel
{
    protected $table = 'wl_pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'status',
        'views',
    ];
}
