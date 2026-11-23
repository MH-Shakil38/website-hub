<?php

namespace Modules\HajjUmrah\App\Models;

class HajjInquiry extends HajjUmrahBaseModel
{
    protected $table = 'inquiries';
    protected $guarded = [];

    public function getPilgrimCountAttribute(): string
    {
        return (string) ($this->attributes['travelers_count'] ?? '1');
    }

    public function getTravelDateAttribute(): ?string
    {
        return $this->attributes['preferred_date'] ?? null;
    }
}
