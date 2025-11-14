<?php

namespace App\Models;

use App\ActivityType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity_date',
        'activity_type',
        'related_id',
        'related_type',
        'points_earned',
        'is_same_day',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'date',
            'activity_type' => ActivityType::class,
            'is_same_day' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function related(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'related_type', 'related_id')->withDefault();
    }
}
