<?php

namespace Modules\RealEstate\App\Models;

class ModuleSetting extends RealEstateBaseModel
{
    protected $table = 'settings';
    protected $guarded = [];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, mixed $value, string $group = 'general'): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }
}
