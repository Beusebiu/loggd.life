<?php

namespace App\Observers;

use App\ActivityType;
use App\Models\WeeklyReview;
use App\Services\GamificationService;
use Carbon\Carbon;

class WeeklyReviewObserver
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function created(WeeklyReview $review): void
    {
        $this->gamificationService->logActivity(
            $review->user,
            ActivityType::WeeklyReview,
            Carbon::parse($review->week_start_date),
            $review->id,
            WeeklyReview::class
        );
    }

    public function updated(WeeklyReview $review): void
    {
        // Update activity log for edits
        $this->gamificationService->logActivity(
            $review->user,
            ActivityType::WeeklyReview,
            Carbon::parse($review->week_start_date),
            $review->id,
            WeeklyReview::class
        );
    }
}
