<?php

namespace Modules\RealEstate\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class RealEstateBaseModel extends Model
{
    protected string $modulePrefix = 're_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }
}
