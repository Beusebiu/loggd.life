<?php

namespace App\Providers;

use App\Models\DailyCheckIn;
use App\Models\GoalMilestone;
use App\Models\GoalUpdate;
use App\Models\HabitCheck;
use App\Models\Vision;
use App\Models\WeeklyReview;
use App\Observers\DailyCheckInObserver;
use App\Observers\GoalMilestoneObserver;
use App\Observers\GoalUpdateObserver;
use App\Observers\HabitCheckObserver;
use App\Observers\VisionObserver;
use App\Observers\WeeklyReviewObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers for gamification activity logging
        HabitCheck::observe(HabitCheckObserver::class);
        GoalMilestone::observe(GoalMilestoneObserver::class);
        GoalUpdate::observe(GoalUpdateObserver::class);
        DailyCheckIn::observe(DailyCheckInObserver::class);
        WeeklyReview::observe(WeeklyReviewObserver::class);
        Vision::observe(VisionObserver::class);
    }
}
