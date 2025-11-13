<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalTask extends Model
{
    protected $fillable = [
        'goal_id',
        'milestone_id',
        'title',
        'description',
        'completed',
        'due_date',
        'completed_at',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(GoalMilestone::class, 'milestone_id');
    }

    public function markAsCompleted(): void
    {
        $this->completed = true;
        $this->completed_at = now();
        $this->save();

        // Update parent goal progress
        $this->goal->updateProgress();
    }

    public function toggle(): void
    {
        if ($this->completed) {
            $this->completed = false;
            $this->completed_at = null;
        } else {
            $this->markAsCompleted();
        }

        $this->save();
    }
}
