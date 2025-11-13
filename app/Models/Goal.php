<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'life_area',
        'time_horizon',
        'tracking_type',
        'metric_unit',
        'metric_start_value',
        'metric_target_value',
        'metric_current_value',
        'metric_decrease',
        'period_identifier',
        'parent_goal_id',
        'status',
        'progress_percentage',
        'target_date',
        'started_at',
        'completed_at',
        'notes',
        'last_update_note',
        'last_reviewed_at',
        'custom_fields',
        'is_public',
        'allow_comments',
        'celebration_count',
        'vision_life_area',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'custom_fields' => 'array',
            'is_public' => 'boolean',
            'allow_comments' => 'boolean',
            'metric_decrease' => 'boolean',
            'target_date' => 'date',
            'started_at' => 'date',
            'completed_at' => 'datetime',
            'last_reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(GoalMilestone::class)->orderBy('order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(GoalTask::class)->orderBy('order');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(GoalMetric::class)->orderBy('order');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(GoalUpdate::class)->orderBy('update_date', 'desc');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Goal::class, 'parent_goal_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Goal::class, 'parent_goal_id')->orderBy('order');
    }

    public function completedTasks(): HasMany
    {
        return $this->tasks()->where('completed', true);
    }

    public function pendingTasks(): HasMany
    {
        return $this->tasks()->where('completed', false);
    }

    public function completedMilestones(): HasMany
    {
        return $this->milestones()->where('completed', true);
    }

    public function markAsCompleted(): void
    {
        $this->status = 'completed';
        $this->progress_percentage = 100;
        $this->completed_at = now();
        $this->save();
    }

    public function updateProgress(): void
    {
        // For milestone tracking, calculate from milestones
        if ($this->tracking_type === 'milestone') {
            $totalMilestones = $this->milestones()->count();

            if ($totalMilestones === 0) {
                $this->progress_percentage = 0;
            } else {
                $completedMilestones = $this->completedMilestones()->count();
                $this->progress_percentage = (int) (($completedMilestones / $totalMilestones) * 100);
            }

            $this->save();
            return;
        }

        // For metric tracking, use the metric progress
        if ($this->tracking_type === 'metric') {
            $this->progress_percentage = $this->getMetricProgressPercentage();
            $this->save();
            return;
        }

        // For evolution/active goals, progress is manually set
    }

    public function celebrate(): void
    {
        $this->increment('celebration_count');
    }

    public function getMetricProgressPercentage(): int
    {
        if ($this->tracking_type !== 'metric' || ! $this->metric_start_value || ! $this->metric_target_value) {
            return 0;
        }

        $current = $this->metric_current_value ?? $this->metric_start_value;
        $start = $this->metric_start_value;
        $target = $this->metric_target_value;

        if ($this->metric_decrease) {
            // For decreasing metrics (mortgage, weight loss)
            $progress = $start - $current;
            $total = $start - $target;
        } else {
            // For increasing metrics (income, customers)
            $progress = $current - $start;
            $total = $target - $start;
        }

        if ($total == 0) {
            return 0;
        }

        return (int) min(100, max(0, ($progress / $total) * 100));
    }

    public function isOnTrack(): bool
    {
        if (! $this->target_date || $this->tracking_type === 'active') {
            return true; // No deadline or ongoing goal
        }

        $totalDays = $this->started_at?->diffInDays($this->target_date) ?? 1;
        $daysPassed = $this->started_at?->diffInDays(now()) ?? 0;

        $expectedProgress = ($daysPassed / $totalDays) * 100;
        $actualProgress = $this->tracking_type === 'metric'
            ? $this->getMetricProgressPercentage()
            : $this->progress_percentage;

        return $actualProgress >= ($expectedProgress - 10); // 10% grace margin
    }

    public function getTrendIndicator(): string
    {
        if ($this->tracking_type !== 'metric') {
            return 'neutral';
        }

        $recentUpdates = $this->updates()
            ->whereNotNull('metric_value')
            ->orderBy('update_date', 'desc')
            ->take(2)
            ->get();

        if ($recentUpdates->count() < 2) {
            return 'neutral';
        }

        $latest = $recentUpdates[0]->metric_value;
        $previous = $recentUpdates[1]->metric_value;

        if ($this->metric_decrease) {
            // For decreasing (lower is better)
            if ($latest < $previous) {
                return 'up';
            } // Getting better
            if ($latest > $previous) {
                return 'down';
            } // Getting worse
        } else {
            // For increasing (higher is better)
            if ($latest > $previous) {
                return 'up';
            } // Getting better
            if ($latest < $previous) {
                return 'down';
            } // Getting worse
        }

        return 'neutral';
    }
}
