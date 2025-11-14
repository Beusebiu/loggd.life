<?php

namespace App\Services;

use App\ActivityType;
use App\Models\User;
use App\Models\UserActivityLog;
use App\UserLevel;
use Carbon\Carbon;

class GamificationService
{
    public function __construct(protected LeaderboardService $leaderboardService) {}

    /**
     * Log an activity and award points
     */
    public function logActivity(
        User $user,
        ActivityType $activityType,
        ?Carbon $activityDate = null,
        ?int $relatedId = null,
        ?string $relatedType = null,
        ?array $metadata = null
    ): UserActivityLog {
        $activityDate = $activityDate ?? now();
        $isSameDay = $activityDate->isToday();

        // Calculate points based on timing
        $basePoints = $activityType->basePoints();
        $pointsEarned = $this->calculatePoints($basePoints, $isSameDay, $activityDate);

        // Create or update activity log
        $activityLog = UserActivityLog::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'activity_date' => $activityDate->toDateString(),
                'activity_type' => $activityType,
                'related_id' => $relatedId,
            ],
            [
                'related_type' => $relatedType,
                'points_earned' => $pointsEarned,
                'is_same_day' => $isSameDay,
                'metadata' => $metadata,
            ]
        );

        // Update user's total points and level
        $this->updateUserStats($user);

        return $activityLog;
    }

    /**
     * Calculate points based on timing (Option C: Hybrid) with multipliers
     */
    protected function calculatePoints(int $basePoints, bool $isSameDay, Carbon $activityDate): int
    {
        $daysDifference = now()->diffInDays($activityDate);

        if ($isSameDay || $daysDifference === 0) {
            // Same day = 100%
            $points = $basePoints;
        } elseif ($daysDifference <= 7) {
            // Next day up to 7 days = 50%
            $points = (int) ($basePoints * 0.5);
        } else {
            // Older than 7 days = 0 points
            return 0;
        }

        // Apply time-based multiplier (Friday, Saturday, Sunday bonus)
        $timeMultiplier = $this->getTimeMultiplier($activityDate);
        $points = (int) ($points * $timeMultiplier);

        // Apply comeback multiplier if active (must be for current user)
        if (auth()->check()) {
            $comebackMultiplier = $this->getComebackMultiplier(auth()->user());
            $points = (int) ($points * $comebackMultiplier);
        }

        return $points;
    }

    /**
     * Get time-based multiplier (weekday/weekend bonus)
     */
    protected function getTimeMultiplier(Carbon $date): float
    {
        $dayOfWeek = $date->dayOfWeek;

        return match ($dayOfWeek) {
            Carbon::FRIDAY, Carbon::SATURDAY => 1.5,
            Carbon::SUNDAY => 2.0,
            default => 1.0,
        };
    }

    /**
     * Get comeback multiplier (3x for 24 hours after 7+ days inactive)
     */
    protected function getComebackMultiplier(User $user): float
    {
        if ($user->comeback_multiplier_active
            && $user->comeback_multiplier_expires_at
            && $user->comeback_multiplier_expires_at > now()) {
            return 3.0;
        }

        return 1.0;
    }

    /**
     * Update user's total points, level, and streak
     */
    public function updateUserStats(User $user): void
    {
        // Calculate total points
        $totalPoints = UserActivityLog::query()
            ->where('user_id', $user->id)
            ->sum('points_earned');

        // Calculate weekly points (current week starting Monday)
        $weekStart = $this->leaderboardService->getWeekStart();
        $weeklyPoints = UserActivityLog::query()
            ->where('user_id', $user->id)
            ->where('activity_date', '>=', $weekStart->toDateString())
            ->sum('points_earned');

        // Calculate level
        $currentLevel = UserLevel::calculateLevel($totalPoints);

        // Calculate streak
        $streak = $this->calculateStreak($user);

        // Check and activate comeback bonus
        $this->leaderboardService->checkAndActivateComebackBonus($user);

        // Update last activity timestamp
        $this->leaderboardService->updateLastActivity($user);

        // Update user
        $user->update([
            'total_points' => $totalPoints,
            'weekly_points' => $weeklyPoints,
            'current_level' => $currentLevel,
            'current_streak' => $streak['current'],
            'longest_streak' => max($user->longest_streak ?? 0, $streak['current']),
            'last_activity_date' => $streak['last_activity_date'],
        ]);

        // Invalidate leaderboard caches for this user
        $this->leaderboardService->invalidateUserCache($user);
    }

    /**
     * Calculate current streak for a user
     */
    protected function calculateStreak(User $user): array
    {
        $activities = UserActivityLog::query()
            ->where('user_id', $user->id)
            ->select('activity_date')
            ->groupBy('activity_date')
            ->orderByDesc('activity_date')
            ->get()
            ->pluck('activity_date')
            ->map(fn ($date) => Carbon::parse($date));

        if ($activities->isEmpty()) {
            return ['current' => 0, 'last_activity_date' => null];
        }

        $lastActivityDate = $activities->first();
        $daysSinceLastActivity = now()->startOfDay()->diffInDays($lastActivityDate->startOfDay());

        // Streak is broken if more than 1 day gap
        if ($daysSinceLastActivity > 1) {
            return ['current' => 0, 'last_activity_date' => $lastActivityDate];
        }

        $currentStreak = 1;
        $previousDate = $lastActivityDate;

        foreach ($activities->skip(1) as $activityDate) {
            $daysDiff = $previousDate->startOfDay()->diffInDays($activityDate->startOfDay());

            if ($daysDiff === 1) {
                $currentStreak++;
                $previousDate = $activityDate;
            } else {
                break;
            }
        }

        return ['current' => $currentStreak, 'last_activity_date' => $lastActivityDate];
    }

    /**
     * Get activity grid data for a user (365 days)
     */
    public function getActivityGridData(User $user, ?int $year = null): array
    {
        $year = $year ?? now()->year;
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31);

        // Get all activities for the year with details
        $activities = UserActivityLog::query()
            ->where('user_id', $user->id)
            ->whereBetween('activity_date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn ($activity) => Carbon::parse($activity->activity_date)->toDateString());

        // Build 365-day grid
        $gridData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate && $currentDate <= now()) {
            $dateString = $currentDate->toDateString();
            $dayActivities = $activities->get($dateString);

            if ($dayActivities) {
                $totalPoints = $dayActivities->sum('points_earned');
                $activityBreakdown = $dayActivities->groupBy('activity_type')->map(function ($group) {
                    return [
                        'type' => $group->first()->activity_type->label(),
                        'count' => $group->count(),
                        'points' => $group->sum('points_earned'),
                    ];
                })->values()->toArray();

                $gridData[] = [
                    'date' => $dateString,
                    'points' => $totalPoints,
                    'activity_count' => $dayActivities->count(),
                    'intensity' => $this->calculateIntensity($totalPoints),
                    'activities' => $activityBreakdown,
                ];
            } else {
                $gridData[] = [
                    'date' => $dateString,
                    'points' => 0,
                    'activity_count' => 0,
                    'intensity' => 0,
                    'activities' => [],
                ];
            }

            $currentDate->addDay();
        }

        return $gridData;
    }

    /**
     * Calculate intensity level for grid coloring (0-4)
     */
    protected function calculateIntensity(int $points): int
    {
        return match (true) {
            $points === 0 => 0,
            $points <= 5 => 1,
            $points <= 10 => 2,
            $points <= 15 => 3,
            default => 4,
        };
    }

    /**
     * Get level information for a user
     */
    public function getLevelInfo(User $user): array
    {
        $tier = UserLevel::getTierForLevel($user->current_level);
        $currentLevelPoints = UserLevel::pointsForLevel($user->current_level);
        $nextLevelPoints = UserLevel::pointsForLevel($user->current_level + 1);
        $pointsIntoCurrentLevel = $user->total_points - $currentLevelPoints;
        $pointsNeededForNextLevel = $nextLevelPoints - $currentLevelPoints;
        $progressPercentage = $pointsNeededForNextLevel > 0
            ? round(($pointsIntoCurrentLevel / $pointsNeededForNextLevel) * 100)
            : 100;

        return [
            'level' => $user->current_level,
            'tier' => $tier,
            'tier_name' => $tier->tierName(),
            'emoji' => $tier->emoji(),
            'total_points' => $user->total_points,
            'current_level_points' => $currentLevelPoints,
            'next_level_points' => $nextLevelPoints,
            'points_into_level' => $pointsIntoCurrentLevel,
            'points_needed' => $nextLevelPoints - $user->total_points,
            'progress_percentage' => $progressPercentage,
            'color_theme' => $tier->colorTheme(),
        ];
    }

    /**
     * Check if user can edit an activity (within 7 days)
     */
    public function canEditActivity(Carbon $activityDate): bool
    {
        return now()->diffInDays($activityDate) <= 7;
    }
}
