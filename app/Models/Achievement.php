<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'user_id',
        'achievement_type',
        'metadata',
        'shown_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'shown_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the achievement.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the achievement as shown.
     */
    public function markAsShown(): void
    {
        $this->update(['shown_at' => now()]);
    }

    /**
     * Scope to get only unshown achievements.
     */
    public function scopeUnshown($query)
    {
        return $query->whereNull('shown_at');
    }

    /**
     * Scope to get achievements for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
