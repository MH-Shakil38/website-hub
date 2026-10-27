<?php

namespace Modules\Interior\App\Models;

class Project extends InteriorBaseModel
{
    protected $table = 'int_projects';

    protected $fillable = [
        'title',
        'slug',
        'category',
        'image',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
