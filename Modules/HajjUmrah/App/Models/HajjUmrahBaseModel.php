<?php

namespace Modules\HajjUmrah\App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class HajjUmrahBaseModel extends Model
{
    protected $connection = 'tenant';

    protected string $modulePrefix = 'hajj_';

    public function getTable(): string
    {
        $table = parent::getTable();

        return str_starts_with($table, $this->modulePrefix)
            ? $table
            : $this->modulePrefix . $table;
    }

    /**
     * Resolve localized field (e.g. $model->trans('title', $locale))
     * Automatically falls back to English ('en') or base field if translation is empty.
     */
    public function trans(string $field, ?string $locale = null): mixed
    {
        $loc = $locale ?: session('hajj_locale', app()->getLocale() ?: 'en');
        if (!in_array($loc, ['en', 'bn', 'ar'], true)) {
            $loc = 'en';
        }

        if ($loc !== 'en') {
            $locColumn = "{$field}_{$loc}";
            if (!empty($this->{$locColumn})) {
                return $this->{$locColumn};
            }
        }

        return $this->{$field} ?? '';
    }
}
