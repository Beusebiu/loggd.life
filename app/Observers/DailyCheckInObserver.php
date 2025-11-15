<?php

namespace App\Observers;

use App\ActivityType;
use App\Models\DailyCheckIn;
use App\Services\GamificationService;
use Carbon\Carbon;

class DailyCheckInObserver
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function created(DailyCheckIn $checkIn): void
    {
        $this->gamificationService->logActivity(
            $checkIn->user,
            ActivityType::DailyReflection,
            Carbon::parse($checkIn->date),
            $checkIn->id,
            DailyCheckIn::class
        );
    }

    public function updated(DailyCheckIn $checkIn): void
    {
        // Update activity log on same day edits
        // The logActivity method handles updateOrCreate, so this is safe
        $this->gamificationService->logActivity(
            $checkIn->user,
            ActivityType::DailyReflection,
            Carbon::parse($checkIn->date),
            $checkIn->id,
            DailyCheckIn::class
        );
    }
}
