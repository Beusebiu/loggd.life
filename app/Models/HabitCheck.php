<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitCheck extends Model
{
    protected $fillable = [
        'habit_id',
        'user_id',
        'date',
        'time',
        'checked',
        'note',
        'checked_at',
    ];

    protected $casts = [
        'date' => 'date',
        'checked' => 'boolean',
        'checked_at' => 'datetime',
    ];

    /**
     * Serialize date as Y-m-d format for API responses.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d');
    }

    /**
     * Get the habit that owns the check.
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    /**
     * Get the user that owns the check.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
