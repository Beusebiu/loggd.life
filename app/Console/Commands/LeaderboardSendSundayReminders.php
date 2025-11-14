<?php

namespace App\Console\Commands;

use App\Services\LeaderboardService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LeaderboardSendSundayReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:send-sunday-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Sunday night reminders to top 20 leaderboard users';

    /**
     * Execute the console command.
     */
    public function handle(LeaderboardService $leaderboardService): int
    {
        $this->info('Sending Sunday reminders to top 20 users...');

        $recipients = $leaderboardService->getSundayReminderRecipients();

        foreach ($recipients as $user) {
            // TODO: Implement notification system
            // For now, just log the reminder
            Log::info("Sunday reminder for user {$user->username} (rank #{$user->current_rank})");

            $this->line("Reminder sent to {$user->username} (rank #{$user->current_rank})");
        }

        $this->info("Sent {$recipients->count()} reminders successfully!");

        return Command::SUCCESS;
    }
}
