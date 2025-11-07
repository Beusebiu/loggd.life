<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /**
     * Get comprehensive user statistics.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'overview' => $this->getOverview($user),
            'habits' => $this->getHabitStats($user),
            'entries' => $this->getEntryStats($user),
            'check_ins' => $this->getCheckInStats($user),
            'activity' => $this->getActivityStats($user),
        ]);
    }

    /**
     * Get overview statistics.
     */
    private function getOverview($user)
    {
        return [
            'total_habits' => $user->habits()->count(),
            'active_habits' => $user->habits()->where('status', 'active')->count(),
            'total_entries' => $user->entries()->count(),
            'total_check_ins' => $user->checkIns()->count(),
            'total_habit_checks' => $user->habitChecks()->where('checked', true)->count(),
            'member_since' => $user->created_at->format('Y-m-d'),
            'days_active' => $this->getTotalActiveDays($user),
        ];
    }

    /**
     * Get habit statistics.
     */
    private function getHabitStats($user)
    {
        $habits = $user->habits()->with('checks')->get();
        $habitStats = [];

        foreach ($habits as $habit) {
            $checks = $habit->checks()->where('checked', true)->orderBy('date', 'desc')->get();
            $totalChecks = $checks->count();

            // Calculate current streak
            $currentStreak = 0;
            $today = now()->toDateString();
            $checkDate = $today;

            while (true) {
                $hasCheck = $checks->contains('date', $checkDate);
                if (!$hasCheck) {
                    break;
                }
                $currentStreak++;
                $checkDate = date('Y-m-d', strtotime($checkDate . ' -1 day'));
            }

            // Calculate longest streak
            $longestStreak = $this->calculateLongestStreak($checks);

            // Calculate completion rate (last 30 days)
            $daysAgo30 = now()->subDays(30)->toDateString();
            $checksLast30Days = $habit->checks()
                ->where('checked', true)
                ->where('date', '>=', $daysAgo30)
                ->count();
            $completionRate = min(100, round(($checksLast30Days / 30) * 100));

            $habitStats[] = [
                'id' => $habit->id,
                'name' => $habit->name,
                'emoji' => $habit->emoji,
                'status' => $habit->status,
                'total_checks' => $totalChecks,
                'current_streak' => $currentStreak,
                'longest_streak' => $longestStreak,
                'completion_rate_30d' => $completionRate,
                'started_at' => $habit->start_date,
            ];
        }

        return $habitStats;
    }

    /**
     * Calculate longest streak for a habit.
     */
    private function calculateLongestStreak($checks)
    {
        if ($checks->isEmpty()) {
            return 0;
        }

        $dates = $checks->pluck('date')->map(fn($d) => Carbon::parse($d))->sortBy(fn($d) => $d->timestamp)->values();
        $longestStreak = 1;
        $currentStreak = 1;

        for ($i = 1; $i < count($dates); $i++) {
            $diff = $dates[$i]->diffInDays($dates[$i - 1]);
            if ($diff === 1) {
                $currentStreak++;
                $longestStreak = max($longestStreak, $currentStreak);
            } else {
                $currentStreak = 1;
            }
        }

        return $longestStreak;
    }

    /**
     * Get entry statistics.
     */
    private function getEntryStats($user)
    {
        $entries = $user->entries()->with('category')->get();

        // Entries by category
        $byCategory = $entries->groupBy('category_id')->map(function ($categoryEntries) {
            $first = $categoryEntries->first();
            return [
                'category' => $first->category ? [
                    'name' => $first->category->name,
                    'icon' => $first->category->icon,
                    'color' => $first->category->color,
                ] : null,
                'count' => $categoryEntries->count(),
            ];
        })->values();

        // Entries per month (last 12 months)
        $monthlyEntries = $user->entries()
            ->where('date', '>=', now()->subMonths(12)->startOfMonth())
            ->get()
            ->groupBy(function ($entry) {
                return Carbon::parse($entry->date)->format('Y-m');
            })
            ->map(fn($entries) => $entries->count())
            ->toArray();

        return [
            'total' => $entries->count(),
            'by_category' => $byCategory,
            'monthly' => $monthlyEntries,
            'average_per_week' => round($entries->count() / max(1, now()->diffInWeeks($user->created_at)), 1),
        ];
    }

    /**
     * Get check-in statistics.
     */
    private function getCheckInStats($user)
    {
        $checkIns = $user->checkIns()->get();

        $byType = $checkIns->groupBy('type')->map(fn($items) => $items->count())->toArray();

        return [
            'total' => $checkIns->count(),
            'by_type' => $byType,
            'last_check_in' => $checkIns->sortByDesc('date')->first()?->date,
        ];
    }

    /**
     * Get activity statistics over time.
     */
    private function getActivityStats($user)
    {
        // Activity by day of week
        $habitChecks = $user->habitChecks()
            ->where('checked', true)
            ->where('date', '>=', now()->subMonths(3))
            ->get();

        $byDayOfWeek = $habitChecks->groupBy(function ($check) {
            return Carbon::parse($check->date)->dayOfWeek;
        })->map(fn($items) => $items->count())->toArray();

        // Map to day names
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $activityByDay = [];
        foreach ($dayNames as $index => $dayName) {
            $activityByDay[$dayName] = $byDayOfWeek[$index] ?? 0;
        }

        // Current month progress
        $currentMonth = now()->format('Y-m');
        $daysInMonth = now()->daysInMonth;
        $activeDaysThisMonth = $this->getActiveDaysInMonth($user, $currentMonth);

        return [
            'by_day_of_week' => $activityByDay,
            'current_month_progress' => [
                'active_days' => $activeDaysThisMonth,
                'total_days' => $daysInMonth,
                'percentage' => round(($activeDaysThisMonth / $daysInMonth) * 100),
            ],
        ];
    }

    /**
     * Get total number of days with any activity.
     */
    private function getTotalActiveDays($user)
    {
        $entryDates = $user->entries()->pluck('date')->map(fn($d) => Carbon::parse($d)->toDateString());
        $checkInDates = $user->checkIns()->pluck('date')->map(fn($d) => Carbon::parse($d)->toDateString());
        $habitCheckDates = $user->habitChecks()
            ->where('checked', true)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        return collect([])
            ->merge($entryDates)
            ->merge($checkInDates)
            ->merge($habitCheckDates)
            ->unique()
            ->count();
    }

    /**
     * Get active days in a specific month.
     */
    private function getActiveDaysInMonth($user, $month)
    {
        $startOfMonth = Carbon::parse($month)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::parse($month)->endOfMonth()->toDateString();

        $entryDates = $user->entries()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        $checkInDates = $user->checkIns()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        $habitCheckDates = $user->habitChecks()
            ->where('checked', true)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString());

        return collect([])
            ->merge($entryDates)
            ->merge($checkInDates)
            ->merge($habitCheckDates)
            ->unique()
            ->count();
    }
}
