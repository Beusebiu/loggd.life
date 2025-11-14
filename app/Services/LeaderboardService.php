<?php

namespace App\Services;

use App\Models\User;
use App\Models\WeeklyLeaderboardSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LeaderboardService
{
    /**
     * Get all-time leaderboard (top players by total_points)
     */
    public function getAllTimeLeaderboard(int $limit = 100): Collection
    {
        return Cache::remember('leaderboard:all-time:'.$limit, now()->addHours(6), function () use ($limit) {
            return User::select('id', 'username', 'name', 'total_points', 'current_level')
                ->orderBy('total_points', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($user, $index) {
                    $user->rank = $index + 1;
                    $user->level = $user->current_level;

                    return $user;
                });
        });
    }

    /**
     * Get weekly leaderboard (top players by weekly_points)
     */
    public function getWeeklyLeaderboard(int $limit = 100): Collection
    {
        return Cache::remember('leaderboard:weekly:'.$limit, now()->addHour(), function () use ($limit) {
            return User::select('id', 'username', 'name', 'weekly_points', 'current_level')
                ->orderBy('weekly_points', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($user, $index) {
                    $user->rank = $index + 1;
                    $user->level = $user->current_level;

                    return $user;
                });
        });
    }

    /**
     * Get user's rank in all-time leaderboard
     */
    public function getUserAllTimeRank(User $user): array
    {
        $cacheKey = 'leaderboard:user-rank:'.$user->id.':all-time';

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($user) {
            $rank = User::where('total_points', '>', $user->total_points)->count() + 1;
            $totalPlayers = User::count();

            return [
                'rank' => $rank,
                'total_players' => $totalPlayers,
                'points' => $user->total_points,
                'type' => 'all-time',
            ];
        });
    }

    /**
     * Get user's rank in weekly leaderboard
     */
    public function getUserWeeklyRank(User $user): array
    {
        $cacheKey = 'leaderboard:user-rank:'.$user->id.':weekly';

        return Cache::remember($cacheKey, now()->addHour(), function () use ($user) {
            $rank = User::where('weekly_points', '>', $user->weekly_points)->count() + 1;
            $totalPlayers = User::where('weekly_points', '>', 0)->count();

            return [
                'rank' => $rank,
                'total_players' => $totalPlayers,
                'points' => $user->weekly_points,
                'type' => 'weekly',
            ];
        });
    }

    /**
     * Get near-miss notification for user (within 10 ranks)
     */
    public function getNearMissNotification(User $user): ?array
    {
        $weeklyRank = $this->getUserWeeklyRank($user);

        if ($weeklyRank['rank'] <= 1 || $weeklyRank['rank'] > 10) {
            return null;
        }

        // Get the user directly above them
        $userAbove = User::select('id', 'username', 'weekly_points')
            ->where('weekly_points', '>', $user->weekly_points)
            ->orderBy('weekly_points', 'asc')
            ->first();

        if (! $userAbove) {
            return null;
        }

        $pointsDifference = $userAbove->weekly_points - $user->weekly_points;
        $targetRank = $weeklyRank['rank'] - 1;

        return [
            'target_rank' => $targetRank,
            'target_username' => $userAbove->username,
            'points_needed' => $pointsDifference,
            'message' => "You're {$pointsDifference} points behind @{$userAbove->username} for #{$targetRank} this week!",
        ];
    }

    /**
     * Check and activate comeback bonus if user has been inactive 7+ days
     */
    public function checkAndActivateComebackBonus(User $user): bool
    {
        // If already active, don't reactivate
        if ($user->comeback_multiplier_active && $user->comeback_multiplier_expires_at > now()) {
            return false;
        }

        // Check if last activity was 7+ days ago
        if (! $user->last_activity_at || $user->last_activity_at->diffInDays(now()) < 7) {
            return false;
        }

        // Activate 24-hour comeback bonus
        $user->update([
            'comeback_multiplier_active' => true,
            'comeback_multiplier_expires_at' => now()->addDay(),
        ]);

        return true;
    }

    /**
     * Reset weekly leaderboard (run every Monday at midnight)
     */
    public function resetWeeklyLeaderboard(): void
    {
        $lastWeekStart = now()->startOfWeek()->subWeek();

        // Snapshot current week's rankings
        $users = User::select('id', 'weekly_points')
            ->where('weekly_points', '>', 0)
            ->orderBy('weekly_points', 'desc')
            ->get();

        foreach ($users as $index => $user) {
            WeeklyLeaderboardSnapshot::create([
                'user_id' => $user->id,
                'week_start_date' => $lastWeekStart,
                'weekly_points' => $user->weekly_points,
                'rank' => $index + 1,
            ]);
        }

        // Reset all users' weekly points
        User::query()->update(['weekly_points' => 0]);

        // Clear weekly caches
        Cache::tags(['leaderboard:weekly'])->flush();
    }

    /**
     * Get users who should receive Sunday night reminders
     */
    public function getSundayReminderRecipients(): Collection
    {
        // Get users in top 20 who might lose their position
        return User::select('id', 'username', 'email', 'weekly_points')
            ->orderBy('weekly_points', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($user, $index) {
                $user->current_rank = $index + 1;

                return $user;
            });
    }

    /**
     * Update user's last activity timestamp
     */
    public function updateLastActivity(User $user): void
    {
        $user->update(['last_activity_at' => now()]);
    }

    /**
     * Invalidate user's rank caches
     */
    public function invalidateUserCache(User $user): void
    {
        Cache::forget('leaderboard:user-rank:'.$user->id.':all-time');
        Cache::forget('leaderboard:user-rank:'.$user->id.':weekly');
    }

    /**
     * Get week start date (Monday)
     */
    public function getWeekStart(): Carbon
    {
        return now()->startOfWeek();
    }

    /**
     * Get previous week's snapshot for user
     */
    public function getLastWeekRank(User $user): ?array
    {
        $lastWeekStart = now()->startOfWeek()->subWeek();

        $snapshot = WeeklyLeaderboardSnapshot::where('user_id', $user->id)
            ->where('week_start_date', $lastWeekStart)
            ->first();

        if (! $snapshot) {
            return null;
        }

        return [
            'rank' => $snapshot->rank,
            'points' => $snapshot->weekly_points,
            'week_start' => $snapshot->week_start_date,
        ];
    }
}
