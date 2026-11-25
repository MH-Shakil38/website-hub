<?php

namespace Modules\GymFitness\App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class GymPackage extends GymFitnessBaseModel
{
    protected $table = 'packages';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'features_bn' => 'array',
            'monthly_price' => 'decimal:2',
            'quarterly_price' => 'decimal:2',
            'six_month_price' => 'decimal:2',
            'yearly_price' => 'decimal:2',
            'custom_duration_price' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'has_personal_trainer' => 'boolean',
            'has_group_classes' => 'boolean',
            'has_locker' => 'boolean',
            'has_nutrition' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(GymMember::class, 'package_id');
    }

    public function localizedFeatures(?string $locale = null): array
    {
        $loc = $locale ?: session('gym_locale', 'en');
        if ($loc === 'bn' && !empty($this->features_bn) && is_array($this->features_bn)) {
            return $this->features_bn;
        }
        return is_array($this->features) ? $this->features : [];
    }
}
