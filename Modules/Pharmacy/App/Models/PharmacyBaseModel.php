<?php

namespace Modules\Pharmacy\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class PharmacyBaseModel extends Model
{
    protected string $modulePrefix = 'pharma_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }
}
