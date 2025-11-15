<?php

namespace App\Observers;

use App\ActivityType;
use App\Models\GoalUpdate;
use App\Services\GamificationService;
use Carbon\Carbon;

class GoalUpdateObserver
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function created(GoalUpdate $update): void
    {
        $this->gamificationService->logActivity(
            $update->goal->user,
            ActivityType::GoalUpdate,
            $update->update_date ? Carbon::parse($update->update_date) : now(),
            $update->id,
            GoalUpdate::class
        );
    }
}
