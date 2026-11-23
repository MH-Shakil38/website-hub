<?php

namespace Modules\HajjUmrah\App\Models;

class HajjPartner extends HajjUmrahBaseModel
{
    protected $table = 'partners';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function getPartnerTypeAttribute(): string
    {
        $t = strtolower((string) ($this->attributes['type'] ?? 'airline'));
        return match ($t) {
            'review_badge', 'trustbadge' => 'TrustBadge',
            'hotel' => 'Hotel',
            default => 'Airline',
        };
    }

    public function getRatingTextAttribute(): ?string
    {
        return $this->attributes['rating'] ?? null;
    }

    public function getReviewsCountAttribute(): ?string
    {
        return $this->attributes['reviews_text'] ?? null;
    }

    public function getIconClassAttribute(): string
    {
        return 'fa-solid fa-plane';
    }

    public function getBrandColorAttribute(): string
    {
        return '#036939';
    }
}
