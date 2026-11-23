<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class GymFitnessBaseModel extends Model
{
    protected $connection = 'tenant';

    protected string $modulePrefix = 'gym_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }

    /**
     * Resolve localized field (English 'en' default, Bengali 'bn').
     */
    public function trans(string $field, ?string $locale = null): mixed
    {
        $loc = $locale ?: session('gym_locale', 'en');
        if (!in_array($loc, ['en', 'bn'], true)) {
            $loc = 'en';
        }

        if ($loc === 'bn') {
            $locColumn = "{$field}_bn";
            if (!empty($this->{$locColumn})) {
                return $this->{$locColumn};
            }
        }

        return $this->{$field} ?? '';
    }
}
