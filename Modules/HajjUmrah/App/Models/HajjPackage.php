<?php

namespace Modules\HajjUmrah\App\Models;

class HajjPackage extends HajjUmrahBaseModel
{
    protected $table = 'packages';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'features'       => 'array',
            'features_bn'    => 'array',
            'features_ar'    => 'array',
            'is_featured'    => 'boolean',
            'is_active'      => 'boolean',
        ];
    }

    public function localizedFeatures(?string $locale = null): array
    {
        $loc = $locale ?: session('hajj_locale', 'en');
        if ($loc === 'bn' && !empty($this->features_bn) && is_array($this->features_bn)) {
            return $this->features_bn;
        }
        if ($loc === 'ar' && !empty($this->features_ar) && is_array($this->features_ar)) {
            return $this->features_ar;
        }

        return is_array($this->features) ? $this->features : [];
    }

    public function getPriceDisplayAttribute(): string
    {
        return (string) ($this->attributes['price'] ?? '$15,000');
    }

    public function getFlightInfoAttribute(): ?string
    {
        return $this->attributes['airline'] ?? null;
    }

    public function getIsSpecialOfferAttribute(): bool
    {
        return (bool) ($this->attributes['is_featured'] ?? true);
    }
}
