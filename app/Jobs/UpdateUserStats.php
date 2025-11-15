<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateUserStats implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GamificationService $gamificationService): void
    {
        $gamificationService->updateUserStats($this->user);
    }
}
