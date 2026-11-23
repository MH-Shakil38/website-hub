<?php

namespace Modules\WonderLearn\App\Models;

class WlGame extends WonderLearnBaseModel
{
    protected $table = 'wl_games';

    protected $fillable = [
        'title',
        'slug',
        'category',
        'game_type',
        'question_prompt',
        'correct_answer',
        'options_json',
        'image',
        'card_bg',
        'play_count',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'options_json' => 'array',
            'is_active'    => 'boolean',
        ];
    }
}
