<?php

namespace Modules\Interior\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class InteriorBaseModel extends Model
{
    protected string $modulePrefix = 'int_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }
}
