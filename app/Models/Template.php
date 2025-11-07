<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'behavior',
        'period',
        'structure',
        'icon',
        'is_system',
        'is_hidden',
        'order',
    ];

    protected $casts = [
        'structure' => 'array',
        'is_system' => 'boolean',
        'is_hidden' => 'boolean',
    ];

    /**
     * Get the user that owns the template (null for system templates).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the check-ins that use this template.
     */
    public function checkIns(): HasMany
    {
        return $this->hasMany(CheckIn::class);
    }

    /**
     * Scope a query to only include system templates.
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    /**
     * Scope a query to only include user templates.
     */
    public function scopeUserTemplates($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query by category.
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
