<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyLeaderboardSnapshot extends Model
{
    protected $fillable = [
        'user_id',
        'week_start_date',
        'weekly_points',
        'rank',
    ];

    protected function casts(): array
    {
        return [
            'week_start_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
