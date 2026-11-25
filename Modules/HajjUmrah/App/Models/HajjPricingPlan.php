<?php

namespace Modules\HajjUmrah\App\Models;

class HajjPricingPlan extends HajjUmrahBaseModel
{
    protected $table = 'pricing_plans';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_active'   => 'boolean',
        ];
    }

    public function getIsHighlightedAttribute(): bool
    {
        return (bool) ($this->attributes['is_featured'] ?? false);
    }

    public function getTaglineAttribute(): ?string
    {
        return $this->attributes['subtitle'] ?? null;
    }

    public function getTaglineBnAttribute(): ?string
    {
        return $this->attributes['subtitle_bn'] ?? null;
    }

    public function getTaglineArAttribute(): ?string
    {
        return $this->attributes['subtitle_ar'] ?? null;
    }

    public function getStartsFromLabelAttribute(): string
    {
        return (string) ($this->attributes['price_suffix'] ?? 'STARTS FROM');
    }

    public function getFoodServiceAttribute(): ?string
    {
        return $this->attributes['food'] ?? null;
    }

    public function getFoodServiceBnAttribute(): ?string
    {
        return $this->attributes['food_bn'] ?? null;
    }

    public function getFoodServiceArAttribute(): ?string
    {
        return $this->attributes['food_ar'] ?? null;
    }

    public function getTermsNoteAttribute(): ?string
    {
        return $this->attributes['terms_text'] ?? '*Terms & Conditions Applicable';
    }

    public function getTermsNoteBnAttribute(): ?string
    {
        return $this->attributes['terms_text_bn'] ?? null;
    }

    public function getTermsNoteArAttribute(): ?string
    {
        return $this->attributes['terms_text_ar'] ?? null;
    }
}
