<?php

namespace Modules\IvfCenter\App\Models;

class ModuleSetting extends IvfCenterBaseModel
{
    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    public static function getVal(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
}
