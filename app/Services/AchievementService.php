<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Habit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    /**
     * Trigger achievement when a habit is completed.
     */
    public function triggerHabitCompletion(User $user, Habit $habit, int $currentStreak): void
    {
        \Log::info('Achievement triggered', [
            'user_id' => $user->id,
            'habit_id' => $habit->id,
            'habit_name' => $habit->name,
            'current_streak' => $currentStreak,
            'is_milestone' => $this->isStreakMilestone($currentStreak),
        ]);

        // Check for streak milestones first (they take priority)
        if ($this->isStreakMilestone($currentStreak)) {
            \Log::info('Creating streak milestone achievement', ['streak' => $currentStreak]);
            $this->createAchievement($user, "streak_{$currentStreak}_days", [
                'habit_id' => $habit->id,
                'habit_name' => $habit->name,
                'habit_emoji' => $habit->emoji,
                'streak_count' => $currentStreak,
            ]);

            return;
        }

        // Check if this is their very first habit check ever
        if ($this->isFirstHabitCompletion($user)) {
            \Log::info('Creating first habit achievement');
            $this->createAchievement($user, 'first_habit', [
                'habit_id' => $habit->id,
                'habit_name' => $habit->name,
                'habit_emoji' => $habit->emoji,
            ]);

            return;
        }

        // Regular habit completion (per-habit cooldown to avoid spam)
        if ($this->shouldTriggerRegularCompletion($user, $habit->id)) {
            \Log::info('Creating regular habit completion achievement');
            $this->createAchievement($user, 'habit_completed', [
                'habit_id' => $habit->id,
                'habit_name' => $habit->name,
                'habit_emoji' => $habit->emoji,
                'streak_count' => $currentStreak,
            ]);
        } else {
            \Log::info('Skipping achievement due to cooldown');
        }
    }

    /**
     * Trigger achievement when a check-in is created.
     */
    public function triggerCheckInCreated(User $user, string $checkInType): void
    {
        $this->createAchievement($user, 'check_in_created', [
            'check_in_type' => $checkInType,
        ]);
    }

    /**
     * Trigger achievement for a perfect week (all habits completed every day).
     */
    public function triggerPerfectWeek(User $user): void
    {
        $this->createAchievement($user, 'perfect_week', [
            'week_start' => now()->startOfWeek()->toDateString(),
            'week_end' => now()->endOfWeek()->toDateString(),
        ]);
    }

    /**
     * Check if a user has completed a perfect week.
     */
    public function checkPerfectWeek(User $user): bool
    {
        $habits = $user->habits()->where('status', 'active')->get();

        if ($habits->isEmpty()) {
            return false;
        }

        $startOfWeek = now()->startOfWeek();
        $today = now();

        // Check each day of the week so far
        for ($date = $startOfWeek->copy(); $date <= $today; $date->addDay()) {
            $dateStr = $date->toDateString();

            // For each habit, check if it was completed on this day
            foreach ($habits as $habit) {
                $check = DB::table('habit_checks')
                    ->where('user_id', $user->id)
                    ->where('habit_id', $habit->id)
                    ->where('date', $dateStr)
                    ->exists();

                if (! $check) {
                    return false; // Found a habit that wasn't completed
                }
            }
        }

        return true;
    }

    /**
     * Get pending (unshown) achievements for a user.
     */
    public function getPendingAchievements(User $user): array
    {
        return Achievement::forUser($user->id)
            ->unshown()
            ->orderBy('created_at', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Mark an achievement as shown.
     */
    public function markAsShown(int $achievementId): void
    {
        $achievement = Achievement::find($achievementId);
        if ($achievement) {
            $achievement->markAsShown();
        }
    }

    /**
     * Check if the given streak is a milestone worth celebrating.
     */
    protected function isStreakMilestone(int $streak): bool
    {
        return in_array($streak, [3, 7, 14, 30, 60, 90, 365]);
    }

    /**
     * Check if this is the user's first ever habit completion.
     */
    protected function isFirstHabitCompletion(User $user): bool
    {
        $totalChecks = DB::table('habit_checks')
            ->where('user_id', $user->id)
            ->count();

        return $totalChecks === 1; // Just completed their first check
    }

    /**
     * Check if we should trigger a regular completion notification.
     * Prevents spam by only allowing one per habit every 10 seconds.
     */
    protected function shouldTriggerRegularCompletion(User $user, int $habitId): bool
    {
        $recentAchievement = Achievement::forUser($user->id)
            ->where('achievement_type', 'habit_completed')
            ->where('metadata->habit_id', $habitId)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        return ! $recentAchievement;
    }

    /**
     * Create a new achievement record.
     */
    protected function createAchievement(User $user, string $type, array $metadata = []): void
    {
        Achievement::create([
            'user_id' => $user->id,
            'achievement_type' => $type,
            'metadata' => $metadata,
        ]);
    }
}
