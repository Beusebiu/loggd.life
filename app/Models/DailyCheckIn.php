<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyCheckIn extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        // Morning Planning
        'yesterday_priority_completed',
        'yesterday_review',
        'today_priority',
        'today_tasks',
        'goal_work_today',
        'goal_work_details',
        // Evening Reflection
        'day_reflection',
        'mood',
        // Integration
        'goals_worked_on',
        'goal_specific_tasks',
        'habit_completions',
        'notes',
        // Social
        'is_public',
        'share_wins_to_feed',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'yesterday_priority_completed' => 'boolean',
            'goal_work_today' => 'boolean',
            'today_tasks' => 'array',
            'goals_worked_on' => 'array',
            'goal_specific_tasks' => 'array',
            'habit_completions' => 'array',
            'is_public' => 'boolean',
            'share_wins_to_feed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function goalsWorkedOn()
    {
        if (empty($this->goals_worked_on)) {
            return collect();
        }

        return Goal::whereIn('id', $this->goals_worked_on)->get();
    }
}
