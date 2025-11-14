<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Illuminate\Console\Command;

class LeaderboardResetWeekly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:reset-weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset weekly leaderboard and create snapshot of previous week';

    /**
     * Execute the console command.
     */
    public function handle(LeaderboardService $leaderboardService): int
    {
        $this->info('Resetting weekly leaderboard...');

        $leaderboardService->resetWeeklyLeaderboard();

        $this->info('Weekly leaderboard reset complete!');

        return Command::SUCCESS;
    }
}
