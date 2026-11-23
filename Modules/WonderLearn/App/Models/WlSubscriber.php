<?php

namespace Modules\WonderLearn\App\Models;

class WlSubscriber extends WonderLearnBaseModel
{
    protected $table = 'wl_subscribers';

    protected $fillable = [
        'email',
        'status',
    ];
}
