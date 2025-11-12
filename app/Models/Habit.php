<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Habit extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'emoji',
        'frequency',
        'custom_days',
        'description',
        'color',
        'start_date',
        'status',
        'allow_multiple_checks',
        'is_public',
    ];

    protected $casts = [
        'custom_days' => 'array',
        'start_date' => 'date',
        'allow_multiple_checks' => 'boolean',
        'is_public' => 'boolean',
    ];

    /**
     * Get the user that owns the habit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the checks for this habit.
     */
    public function checks(): HasMany
    {
        return $this->hasMany(HabitCheck::class);
    }

    /**
     * Scope a query to only include active habits.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include archived habits.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope a query to only include public habits.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include private habits.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }
}
