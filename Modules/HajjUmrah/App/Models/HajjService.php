<?php

namespace Modules\HajjUmrah\App\Models;

class HajjService extends HajjUmrahBaseModel
{
    protected $table = 'services';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
