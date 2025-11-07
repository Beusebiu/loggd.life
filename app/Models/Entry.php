<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entry extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'content',
        'date',
        'is_public',
    ];

    protected $casts = [
        'date' => 'date',
        'is_public' => 'boolean',
    ];

    /**
     * Get the user that owns the entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category for this entry.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to only include public entries.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include private entries.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }
}
