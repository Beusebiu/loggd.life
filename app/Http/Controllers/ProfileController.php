<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Goal;
use App\Models\GoalMilestone;
use App\Models\User;
use App\Models\Vision;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the specified user's profile.
     */
    public function show(Request $request, string $username)
    {
        // Remove @ if it's included in the URL
        $username = ltrim($username, '@');

        // Find user by username
        $user = User::where('username', $username)->firstOrFail();

        // Check if the profile belongs to the authenticated user
        $isOwnProfile = $request->user() && $request->user()->id === $user->id;
        $isActualOwner = $isOwnProfile;

        // If preview mode is enabled, show public view even for own profile
        if ($request->has('preview') && $request->preview === 'public' && $isOwnProfile) {
            $isOwnProfile = false;
        }

        // Check if profile is public or if it's the user's own profile
        if (! $isOwnProfile && ! $user->profile_public) {
            return Inertia::render('ProfilePrivate', [
                'profileUser' => [
                    'name' => $user->name,
                    'username' => $user->username,
                ],
            ]);
        }

        // Get all data for profile
        $stats = $this->getUserStats($user, $isOwnProfile);
        $publicHabits = $this->getPublicHabits($user, $isOwnProfile);
        $publicGoals = $this->getPublicGoals($user, $isOwnProfile);
        $recentWins = $this->getRecentWins($user, $isOwnProfile);
        $activityData = $this->getActivityData($user, $isOwnProfile);
        $visionSnippet = $this->getVisionSnippet($user, $isOwnProfile);
        $milestones = $this->getMilestones($user);

        return Inertia::render('Profile', [
            'profileUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'bio' => $user->bio,
                'profile_public' => $user->profile_public,
                'created_at' => $user->created_at->format('Y-m-d'),
            ],
            'stats' => $stats,
            'publicHabits' => $publicHabits,
            'publicGoals' => $publicGoals,
            'recentWins' => $recentWins,
            'activityData' => $activityData,
            'visionSnippet' => $visionSnippet,
            'milestones' => $milestones,
            'isOwnProfile' => $isOwnProfile,
            'isActualOwner' => $isActualOwner,
        ]);
    }

    /**
     * Get enhanced user statistics
     */
    private function getUserStats(User $user, bool $isOwnProfile)
    {
        $totalHabits = $user->habits()->count();
        $activeHabits = $user->habits()->where('status', 'active')->count();
        $totalChecks = $user->habitChecks()->count();

        // Goals stats
        $activeGoals = $user->goals()->where('status', 'active')->count();
        $completedGoals = $user->goals()->where('status', 'completed')->count();

        // Activity stats
        $totalActiveDays = $user->habitChecks()->selectRaw('DISTINCT date')->count()
                         + $user->dailyCheckIns()->selectRaw('DISTINCT date')->count();

        // Calculate consistency (% of days since account creation)
        $daysSinceCreation = $user->created_at->diffInDays(now()) + 1;
        $consistency = $daysSinceCreation > 0 ? round(($totalActiveDays / $daysSinceCreation) * 100, 1) : 0;

        // Calculate active streak (consecutive days with ANY activity)
        $activeStreak = $this->calculateActiveStreak($user);

        // Achievements count
        $achievements = Achievement::where('user_id', $user->id)->count();

        $stats = [
            'totalHabits' => $totalHabits,
            'activeHabits' => $activeHabits,
            'totalChecks' => $totalChecks,
            'activeGoals' => $activeGoals,
            'completedGoals' => $completedGoals,
            'totalActiveDays' => $totalActiveDays,
            'consistency' => $consistency,
            'activeStreak' => $activeStreak,
            'achievements' => $achievements,
        ];

        // Calculate current streaks for active habits
        $activeHabits = $user->habits()->where('status', 'active')->get();
        $streaks = [];

        foreach ($activeHabits as $habit) {
            $checks = $user->habitChecks()
                ->where('habit_id', $habit->id)
                ->orderBy('date', 'desc')
                ->get();

            $currentStreak = 0;
            $today = now();
            $checkDate = $today->copy();

            // Start from yesterday if today doesn't have a check yet
            if (! $checks->contains('date', $checkDate->toDateString())) {
                $checkDate->subDay();
            }

            while (true) {
                $dateString = $checkDate->toDateString();
                $dayOfWeek = $checkDate->dayOfWeek;

                // Check if this date should be tracked based on habit frequency
                $shouldTrack = $this->shouldTrackDate($habit, $dayOfWeek);

                if ($shouldTrack) {
                    $hasCheck = $checks->contains('date', $dateString);
                    if (! $hasCheck) {
                        break; // Streak ends
                    }
                    $currentStreak++;
                }

                // Move to previous day
                $checkDate->subDay();

                // Safety: don't go back more than 1 year
                if ($checkDate->diffInDays($today) > 365) {
                    break;
                }
            }

            if ($currentStreak > 0) {
                $streaks[] = [
                    'habit_name' => $habit->name,
                    'habit_emoji' => $habit->emoji,
                    'streak' => $currentStreak,
                ];
            }
        }

        // Sort by streak length
        usort($streaks, fn ($a, $b) => $b['streak'] <=> $a['streak']);

        $stats['streaks'] = $streaks;
        $stats['longestStreak'] = ! empty($streaks) ? $streaks[0]['streak'] : 0;

        return $stats;
    }

    /**
     * Check if a date should be tracked based on habit frequency
     */
    private function shouldTrackDate($habit, int $dayOfWeek): bool
    {
        switch ($habit->frequency) {
            case 'daily':
                return true;

            case 'weekdays':
                // Monday = 1, Friday = 5
                return $dayOfWeek >= 1 && $dayOfWeek <= 5;

            case 'weekends':
                // Saturday = 6, Sunday = 0
                return $dayOfWeek === 0 || $dayOfWeek === 6;

            case 'custom':
                // Check if dayOfWeek is in custom_days array
                if (! $habit->custom_days) {
                    return true; // Default to true if custom_days not set
                }

                return in_array($dayOfWeek, $habit->custom_days);

            default:
                return true;
        }
    }

    /**
     * Show the user's habits page
     */
    public function habits(Request $request, string $username)
    {
        $username = ltrim($username, '@');
        $user = User::where('username', $username)->firstOrFail();
        $isOwnProfile = $request->user() && $request->user()->id === $user->id;

        if (! $isOwnProfile) {
            abort(403, 'You can only view your own habits.');
        }

        return Inertia::render('Habits');
    }

    /**
     * Calculate active streak (consecutive days with ANY activity)
     */
    private function calculateActiveStreak(User $user): int
    {
        // Get all dates with habit checks
        $habitCheckDates = $user->habitChecks()
            ->where('checked', true)
            ->selectRaw('DISTINCT date')
            ->pluck('date')
            ->map(fn ($date) => $date instanceof \Carbon\Carbon ? $date->toDateString() : $date)
            ->toArray();

        // Get all dates with daily check-ins
        $dailyCheckInDates = $user->dailyCheckIns()
            ->selectRaw('DISTINCT date')
            ->pluck('date')
            ->map(fn ($date) => $date instanceof \Carbon\Carbon ? $date->toDateString() : $date)
            ->toArray();

        // Merge and get unique dates
        $allActiveDates = array_unique(array_merge($habitCheckDates, $dailyCheckInDates));
        sort($allActiveDates);

        if (empty($allActiveDates)) {
            return 0;
        }

        // Calculate current streak from today backwards
        $streak = 0;
        $checkDate = now()->startOfDay();

        // Start from yesterday if today doesn't have activity yet
        if (! in_array($checkDate->toDateString(), $allActiveDates)) {
            $checkDate->subDay();
        }

        while (true) {
            $dateString = $checkDate->toDateString();

            if (! in_array($dateString, $allActiveDates)) {
                break; // Streak ends
            }

            $streak++;
            $checkDate->subDay();

            // Safety: don't go back more than 1 year
            if ($checkDate->diffInDays(now()) > 365) {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get public habits with detailed information
     */
    private function getPublicHabits(User $user, bool $isOwnProfile): array
    {
        $query = $user->habits()->where('status', 'active');

        // Only show public habits unless it's their own profile
        if (! $isOwnProfile) {
            $query->where('is_public', true);
        }

        $habits = $query->get();
        $habitData = [];

        foreach ($habits as $habit) {
            // Get checks for this habit with count
            $checksWithCount = $user->habitChecks()
                ->where('habit_id', $habit->id)
                ->where('checked', true)
                ->selectRaw('date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get()
                ->mapWithKeys(fn ($check) => [
                    ($check->date instanceof \Carbon\Carbon ? $check->date->toDateString() : $check->date) => $check->count,
                ])
                ->toArray();

            $checks = array_keys($checksWithCount);

            // Calculate current streak
            $currentStreak = 0;
            $today = now();
            $checkDate = $today->copy();

            if (! in_array($checkDate->toDateString(), $checks)) {
                $checkDate->subDay();
            }

            while (true) {
                $dateString = $checkDate->toDateString();
                $dayOfWeek = $checkDate->dayOfWeek;

                if ($this->shouldTrackDate($habit, $dayOfWeek)) {
                    if (! in_array($dateString, $checks)) {
                        break;
                    }
                    $currentStreak++;
                }

                $checkDate->subDay();

                if ($checkDate->diffInDays($today) > 365) {
                    break;
                }
            }

            // Calculate longest streak
            $longestStreak = 0;
            $tempStreak = 0;
            $prevDate = null;

            foreach ($checks as $dateStr) {
                $currentDate = \Carbon\Carbon::parse($dateStr);

                if ($prevDate === null) {
                    $tempStreak = 1;
                } else {
                    $daysBetween = $this->getExpectedDaysBetween($habit, $prevDate, $currentDate);

                    if ($daysBetween === 0) {
                        $tempStreak++;
                    } else {
                        $longestStreak = max($longestStreak, $tempStreak);
                        $tempStreak = 1;
                    }
                }

                $prevDate = $currentDate;
            }
            $longestStreak = max($longestStreak, $tempStreak);

            // Get last 365 days for GitHub-style grid
            $last365Days = [];
            $checkDate = now()->startOfDay();
            for ($i = 364; $i >= 0; $i--) {
                $date = $checkDate->copy()->subDays($i);
                $dateString = $date->toDateString();
                $dayOfWeek = $date->dayOfWeek;

                $shouldTrack = $this->shouldTrackDate($habit, $dayOfWeek);
                $hasCheck = in_array($dateString, $checks);

                $last365Days[] = [
                    'date' => $dateString,
                    'tracked' => $shouldTrack,
                    'completed' => $hasCheck,
                    'count' => $hasCheck ? ($checksWithCount[$dateString] ?? 1) : 0,
                ];
            }

            // Calculate completion rate
            $startDate = \Carbon\Carbon::parse($habit->start_date)->startOfDay();
            $todayDate = now()->startOfDay();
            $totalTrackingDays = $this->countTrackingDays($habit, $startDate, $todayDate);
            $completionRate = $totalTrackingDays > 0 ? round((count($checks) / $totalTrackingDays) * 100, 1) : 0;

            $habitData[] = [
                'id' => $habit->id,
                'name' => $habit->name,
                'emoji' => $habit->emoji,
                'color' => $habit->color,
                'frequency' => $habit->frequency,
                'allow_multiple_checks' => $habit->allow_multiple_checks,
                'current_streak' => $currentStreak,
                'longest_streak' => $longestStreak,
                'year_data' => $last365Days,
                'completion_rate' => $completionRate,
                'start_date' => $habit->start_date,
                'days_tracked' => $habit->created_at->diffInDays(now()),
            ];
        }

        // Sort by current streak (highest first)
        usort($habitData, fn ($a, $b) => $b['current_streak'] <=> $a['current_streak']);

        return $habitData;
    }

    /**
     * Helper method to check if date should be tracked for habit
     */
    private function getExpectedDaysBetween($habit, $prevDate, $currentDate): int
    {
        $checkDate = $prevDate->copy()->addDay();
        $skippedTrackingDays = 0;

        while ($checkDate->lt($currentDate)) {
            if ($this->shouldTrackDate($habit, $checkDate->dayOfWeek)) {
                $skippedTrackingDays++;
            }
            $checkDate->addDay();
        }

        return $skippedTrackingDays;
    }

    /**
     * Helper method to count tracking days for habit
     */
    private function countTrackingDays($habit, $startDate, $endDate): int
    {
        $checkDate = $startDate->copy();
        $trackingDays = 0;

        while ($checkDate->lte($endDate)) {
            if ($this->shouldTrackDate($habit, $checkDate->dayOfWeek)) {
                $trackingDays++;
            }
            $checkDate->addDay();
        }

        return max($trackingDays, 1);
    }

    /**
     * Get public goals with progress information
     */
    private function getPublicGoals(User $user, bool $isOwnProfile): array
    {
        $query = $user->goals();

        // Only show public goals unless it's their own profile
        if (! $isOwnProfile) {
            $query->where('is_public', true);
        }

        $goals = $query->orderBy('created_at', 'desc')->get();
        $goalData = [];

        foreach ($goals as $goal) {
            // Calculate progress based on tracking type
            $progressPercentage = 0;

            if ($goal->tracking_type === 'milestone') {
                $totalMilestones = $goal->milestones()->count();
                $completedMilestones = $goal->milestones()->where('completed', true)->count();
                $progressPercentage = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;
            } elseif ($goal->tracking_type === 'metric') {
                $totalValue = $goal->metrics()->sum('current_value');
                $progressPercentage = $goal->target_value > 0 ? round(($totalValue / $goal->target_value) * 100) : 0;
            }

            // Determine if on track
            $onTrack = false;
            if ($goal->target_date) {
                $daysTotal = \Carbon\Carbon::parse($goal->created_at)->diffInDays($goal->target_date);
                $daysElapsed = \Carbon\Carbon::parse($goal->created_at)->diffInDays(now());
                $expectedProgress = $daysTotal > 0 ? ($daysElapsed / $daysTotal) * 100 : 0;
                $onTrack = $progressPercentage >= $expectedProgress;
            }

            $goalData[] = [
                'id' => $goal->id,
                'title' => $goal->title,
                'time_horizon' => $goal->time_horizon,
                'life_area' => $goal->life_area,
                'progress_percentage' => min($progressPercentage, 100),
                'tracking_type' => $goal->tracking_type,
                'milestones_completed' => $goal->milestones()->where('completed', true)->count(),
                'milestones_total' => $goal->milestones()->count(),
                'target_date' => $goal->target_date?->format('Y-m-d'),
                'on_track' => $onTrack,
                'status' => $goal->status,
            ];
        }

        return $goalData;
    }

    /**
     * Get recent wins (achievements, milestones, streaks)
     */
    private function getRecentWins(User $user, bool $isOwnProfile): array
    {
        $wins = [];

        // Get recent achievements (last 30 days)
        $achievements = Achievement::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($achievements as $achievement) {
            $wins[] = [
                'type' => 'achievement',
                'title' => $achievement->title,
                'description' => $achievement->description,
                'date' => $achievement->created_at->format('Y-m-d'),
                'icon' => '🎉',
            ];
        }

        // Get recent completed goal milestones
        $completedMilestones = GoalMilestone::whereHas('goal', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->where('completed', true)
            ->where('completed_at', '>=', now()->subDays(30))
            ->orderBy('completed_at', 'desc')
            ->get();

        foreach ($completedMilestones as $milestone) {
            $wins[] = [
                'type' => 'milestone',
                'title' => 'Completed milestone: '.$milestone->title,
                'description' => $milestone->goal->title,
                'date' => $milestone->completed_at->format('Y-m-d'),
                'icon' => '✅',
            ];
        }

        // Sort all wins by date
        usort($wins, fn ($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

        return array_slice($wins, 0, 10); // Return top 10
    }

    /**
     * Get activity data for 365-day heatmap
     */
    private function getActivityData(User $user, bool $isOwnProfile): array
    {
        $endDate = now()->endOfDay();
        $startDate = $endDate->copy()->subDays(364)->startOfDay();

        // Get habit check dates
        $habitChecks = $user->habitChecks()
            ->where('date', '>=', $startDate->toDateString())
            ->where('date', '<=', $endDate->toDateString())
            ->where('checked', true)
            ->selectRaw('date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Get daily check-in dates
        $dailyCheckIns = $user->dailyCheckIns()
            ->where('date', '>=', $startDate->toDateString())
            ->where('date', '<=', $endDate->toDateString())
            ->selectRaw('date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Merge activity data
        $activityByDate = [];
        $checkDate = $startDate->copy();

        while ($checkDate->lte($endDate)) {
            $dateString = $checkDate->toDateString();
            $habitCount = $habitChecks[$dateString] ?? 0;
            $checkInCount = $dailyCheckIns[$dateString] ?? 0;
            $totalActivity = $habitCount + $checkInCount;

            $activityByDate[] = [
                'date' => $dateString,
                'count' => $totalActivity,
                'level' => $this->getActivityLevel($totalActivity),
            ];

            $checkDate->addDay();
        }

        return $activityByDate;
    }

    /**
     * Get activity level for heatmap color
     */
    private function getActivityLevel(int $count): int
    {
        if ($count === 0) {
            return 0;
        }
        if ($count <= 2) {
            return 1;
        }
        if ($count <= 4) {
            return 2;
        }
        if ($count <= 7) {
            return 3;
        }

        return 4;
    }

    /**
     * Get vision data for public display
     */
    private function getVisionSnippet(User $user, bool $isOwnProfile): ?array
    {
        $vision = Vision::where('user_id', $user->id)->first();

        if (! $vision) {
            return null;
        }

        // Check if vision is public
        if (! $vision->is_public && ! $isOwnProfile) {
            return null;
        }

        $privateSections = $vision->private_sections ?? [];

        // Collect public sections with their labels
        $sections = [];

        if ($vision->mission_prompt && ! in_array('mission_prompt', $privateSections)) {
            $sections[] = [
                'label' => 'Mission',
                'content' => $vision->mission_prompt,
            ];
        }

        if ($vision->eulogy_method && ! in_array('eulogy_method', $privateSections)) {
            $sections[] = [
                'label' => 'Eulogy',
                'content' => $vision->eulogy_method,
            ];
        }

        if ($vision->bucket_list && ! in_array('bucket_list', $privateSections)) {
            $bucketList = is_string($vision->bucket_list) ? json_decode($vision->bucket_list, true) : $vision->bucket_list;
            if (is_array($bucketList) && ! empty($bucketList)) {
                $sections[] = [
                    'label' => 'Bucket List',
                    'content' => $bucketList,
                ];
            }
        }

        if ($vision->definition_of_success && ! in_array('definition_of_success', $privateSections)) {
            $success = is_string($vision->definition_of_success) ? json_decode($vision->definition_of_success, true) : $vision->definition_of_success;
            // Convert stdClass to array if needed
            if (is_object($success)) {
                $success = json_decode(json_encode($success), true);
            }
            if ((is_array($success) || is_object($success)) && ! empty($success)) {
                $sections[] = [
                    'label' => 'Definition of Success',
                    'content' => $success,
                ];
            }
        }

        if ($vision->odyssey_plan && ! in_array('odyssey_plan', $privateSections)) {
            $odyssey = is_string($vision->odyssey_plan) ? json_decode($vision->odyssey_plan, true) : $vision->odyssey_plan;
            // Convert stdClass to array if needed
            if (is_object($odyssey)) {
                $odyssey = json_decode(json_encode($odyssey), true);
            }
            if ((is_array($odyssey) || is_object($odyssey)) && ! empty($odyssey)) {
                $sections[] = [
                    'label' => 'Odyssey Plan',
                    'content' => $odyssey,
                ];
            }
        }

        if ($vision->future_calendar && ! in_array('future_calendar', $privateSections)) {
            $calendar = is_string($vision->future_calendar) ? json_decode($vision->future_calendar, true) : $vision->future_calendar;
            // Convert stdClass to array if needed
            if (is_object($calendar)) {
                $calendar = json_decode(json_encode($calendar), true);
            }
            if ((is_array($calendar) || is_object($calendar)) && ! empty($calendar)) {
                $sections[] = [
                    'label' => 'Future Calendar',
                    'content' => $calendar,
                ];
            }
        }

        // If own profile, show everything regardless of private settings
        if ($isOwnProfile && empty($sections)) {
            if ($vision->mission_prompt) {
                $sections[] = ['label' => 'Mission', 'content' => $vision->mission_prompt];
            }
            if ($vision->eulogy_method) {
                $sections[] = ['label' => 'Eulogy', 'content' => $vision->eulogy_method];
            }
        }

        if (empty($sections)) {
            return null;
        }

        return $sections;
    }

    /**
     * Get account milestones timeline
     */
    private function getMilestones(User $user): array
    {
        $createdAt = $user->created_at;
        $milestones = [];

        // 30, 60, 90, 180, 365 day milestones
        $milestoneDays = [30, 60, 90, 180, 365];

        foreach ($milestoneDays as $days) {
            $milestoneDate = $createdAt->copy()->addDays($days);
            $isPast = $milestoneDate->isPast();

            $milestones[] = [
                'days' => $days,
                'date' => $milestoneDate->format('M d, Y'),
                'achieved' => $isPast,
                'label' => $days.' Day'.($days > 1 ? 's' : ''),
            ];
        }

        return $milestones;
    }
}
