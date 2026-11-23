<?php

namespace Modules\IvfCenter\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class IvfCenterBaseModel extends Model
{
    protected $connection = 'tenant';

    protected string $modulePrefix = 'ivf_';

    public function getTable(): string
    {
        $table = parent::getTable();
        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }
}
