<?php

namespace App\Observers;

use App\ActivityType;
use App\Models\GoalMilestone;
use App\Services\GamificationService;
use Carbon\Carbon;

class GoalMilestoneObserver
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function updated(GoalMilestone $milestone): void
    {
        // Log activity only when milestone is marked as completed
        if ($milestone->isDirty('completed') && $milestone->completed) {
            $this->gamificationService->logActivity(
                $milestone->goal->user,
                ActivityType::GoalMilestone,
                $milestone->completed_at ? Carbon::parse($milestone->completed_at) : now(),
                $milestone->id,
                GoalMilestone::class
            );
        }
    }
}
