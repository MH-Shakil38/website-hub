<?php

namespace Modules\Clinic\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class ClinicBaseModel extends Model
{
    protected $connection = 'tenant';

    protected string $modulePrefix = 'clinic_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }
}
