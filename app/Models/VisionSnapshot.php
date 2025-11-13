<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisionSnapshot extends Model
{
    protected $fillable = [
        'user_id',
        'eulogy_method',
        'bucket_list',
        'mission_prompt',
        'definition_of_success',
        'odyssey_plan',
        'future_calendar',
        'is_public',
        'private_sections',
        'snapshot_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'bucket_list' => 'array',
            'definition_of_success' => 'object',
            'odyssey_plan' => 'object',
            'future_calendar' => 'object',
            'private_sections' => 'array',
            'is_public' => 'boolean',
            'snapshot_date' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
