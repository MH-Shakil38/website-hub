<?php

namespace Modules\EventManagement\App\Models;

class EmMemory extends EventManagementBaseModel
{
    protected $table = 'em_memories';

    protected $fillable = [
        'title',
        'year_label',
        'image',
        'is_footer_gallery',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_footer_gallery' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
