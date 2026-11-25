<?php

namespace Modules\EventManagement\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class EventManagementBaseModel extends Model
{
    protected $connection = 'tenant';

    protected string $modulePrefix = 'em_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }
}
