<?php

namespace Modules\WonderLearn\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class WonderLearnBaseModel extends Model
{
    protected string $modulePrefix = 'wl_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }
}
