<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalMilestone extends Model
{
    protected $fillable = [
        'goal_id',
        'title',
        'completed',
        'target_date',
        'completed_at',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'target_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function markAsCompleted(): void
    {
        $this->completed = true;
        $this->completed_at = now();
        $this->save();
    }
}
