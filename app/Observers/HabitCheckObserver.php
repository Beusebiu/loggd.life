<?php

namespace App\Observers;

use App\ActivityType;
use App\Models\HabitCheck;
use App\Services\GamificationService;
use Carbon\Carbon;

class HabitCheckObserver
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function created(HabitCheck $check): void
    {
        $this->gamificationService->logActivity(
            $check->user,
            ActivityType::HabitCheck,
            Carbon::parse($check->date),
            $check->id,
            HabitCheck::class
        );
    }

    public function deleted(HabitCheck $check): void
    {
        // When a check is deleted, we should update user stats
        // The activity log entry remains for audit purposes
        $this->gamificationService->updateUserStats($check->user);
    }
}
