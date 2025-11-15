<?php

namespace App\Services;

use App\Models\Goal;
use Carbon\Carbon;

class ProgressCalculationService
{
    /**
     * Calculate progress percentage for a goal based on its tracking type
     */
    public function calculateGoalProgress(Goal $goal): int
    {
        return match ($goal->tracking_type) {
            'milestone' => $this->calculateMilestoneProgress($goal),
            'metric' => $this->calculateMetricProgress($goal),
            default => 0,
        };
    }

    /**
     * Calculate milestone-based progress
     */
    protected function calculateMilestoneProgress(Goal $goal): int
    {
        $totalMilestones = $goal->milestones->count();

        if ($totalMilestones === 0) {
            return 0;
        }

        $completedMilestones = $goal->milestones->where('completed', true)->count();

        return (int) (($completedMilestones / $totalMilestones) * 100);
    }

    /**
     * Calculate metric-based progress
     */
    protected function calculateMetricProgress(Goal $goal): int
    {
        if (! $goal->metric_target_value || $goal->metric_target_value == 0) {
            return 0;
        }

        $current = $goal->metric_current_value ?? 0;
        $target = $goal->metric_target_value;

        if ($goal->metric_decrease) {
            // For decreasing metrics (e.g., weight loss)
            $start = $goal->metric_start_value ?? $target;
            if ($start <= $target) {
                return 100;
            }
            $progress = (($start - $current) / ($start - $target)) * 100;
        } else {
            // For increasing metrics
            $progress = ($current / $target) * 100;
        }

        return (int) min(max($progress, 0), 100);
    }

    /**
     * Determine if a goal is on track based on time elapsed vs progress
     */
    public function isGoalOnTrack(Goal $goal, int $progressPercentage): bool
    {
        if (! $goal->target_date) {
            return true; // No deadline, always on track
        }

        $startDate = $goal->started_at ? Carbon::parse($goal->started_at) : $goal->created_at;
        $targetDate = Carbon::parse($goal->target_date);
        $now = now();

        // If past target date
        if ($now->gt($targetDate)) {
            return $progressPercentage >= 100;
        }

        $daysTotal = $startDate->diffInDays($targetDate);
        if ($daysTotal === 0) {
            return true;
        }

        $daysElapsed = $startDate->diffInDays($now);
        $expectedProgress = ($daysElapsed / $daysTotal) * 100;

        return $progressPercentage >= $expectedProgress;
    }

    /**
     * Calculate completion rate for a set of goals
     */
    public function calculateCompletionRate(int $completed, int $total): float
    {
        if ($total === 0) {
            return 0.0;
        }

        return round(($completed / $total) * 100, 1);
    }
}
