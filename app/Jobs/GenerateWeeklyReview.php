<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\WeeklyReview;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateWeeklyReview implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public Carbon $weekStart
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate weekly review statistics
        $review = WeeklyReview::generateForWeek($this->weekStart->toDateTime(), $this->user);

        // Save the generated review
        WeeklyReview::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'week_start_date' => $this->weekStart->format('Y-m-d'),
            ],
            $review->toArray()
        );
    }
}
