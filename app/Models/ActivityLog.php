<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'core_activity_logs';

    protected $fillable = [
        'user_id',
        'title',
        'subtitle',
        'icon',
        'bg_color',
        'text_color',
        'time_label',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $title,
        string $subtitle,
        string $icon = 'bi-plus-circle-fill',
        string $bgColor = '#DCFCE7',
        string $textColor = '#10B981'
    ): self {
        return self::create([
            'user_id' => auth()->id(),
            'title' => $title,
            'subtitle' => $subtitle,
            'icon' => $icon,
            'bg_color' => $bgColor,
            'text_color' => $textColor,
            'time_label' => 'Just now',
        ]);
    }
}
