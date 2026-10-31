<?php

namespace Modules\Portfolio\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class PortfolioBaseModel extends Model
{
    protected string $modulePrefix = 'port_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }
}
