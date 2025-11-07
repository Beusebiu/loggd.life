<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckIn extends Model
{
    protected $fillable = [
        'user_id',
        'template_id',
        'type',
        'date',
        'content',
        'version',
        'is_active',
        'is_public',
    ];

    protected $casts = [
        'date' => 'date',
        'content' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the check-in.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template for this check-in.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Scope a query to only include public check-ins.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include active check-ins.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
