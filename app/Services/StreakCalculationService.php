<?php

namespace App\Services;

use App\Models\Habit;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StreakCalculationService
{
    /**
     * Calculate current streak for a habit
     */
    public function calculateHabitStreak(Habit $habit, Collection $checks): int
    {
        if ($checks->isEmpty()) {
            return 0;
        }

        $uniqueDates = $checks
            ->where('checked', true)
            ->pluck('date')
            ->map(fn ($date) => $date instanceof Carbon ? $date->toDateString() : $date)
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        if (empty($uniqueDates)) {
            return 0;
        }

        $currentStreak = 0;
        $today = now();
        $checkDate = $today->copy();

        // Start from yesterday if today doesn't have a check yet
        if (! in_array($checkDate->toDateString(), $uniqueDates)) {
            $checkDate->subDay();
        }

        while (true) {
            $dateString = $checkDate->toDateString();
            $dayOfWeek = $checkDate->dayOfWeek;

            // Check if this date should be tracked based on habit frequency
            if ($this->shouldTrackDate($habit, $dayOfWeek)) {
                if (! in_array($dateString, $uniqueDates)) {
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

        return $currentStreak;
    }

    /**
     * Calculate longest streak for a habit
     */
    public function calculateLongestStreak(Habit $habit, array $uniqueDates): int
    {
        if (empty($uniqueDates)) {
            return 0;
        }

        $longestStreak = 0;
        $tempStreak = 0;
        $prevDate = null;

        foreach ($uniqueDates as $dateStr) {
            $currentDate = Carbon::parse($dateStr);

            if ($prevDate === null) {
                $tempStreak = 1;
            } else {
                // Calculate expected days between checks based on frequency
                $daysBetween = $this->getExpectedDaysBetween($habit, $prevDate, $currentDate);

                if ($daysBetween === 0) {
                    // Dates are consecutive tracking days
                    $tempStreak++;
                } else {
                    // Streak broken
                    $longestStreak = max($longestStreak, $tempStreak);
                    $tempStreak = 1;
                }
            }

            $prevDate = $currentDate;
        }

        return max($longestStreak, $tempStreak);
    }

    /**
     * Check if a date should be tracked based on habit frequency
     */
    public function shouldTrackDate(Habit $habit, int $dayOfWeek): bool
    {
        return match ($habit->frequency) {
            'daily' => true,
            'weekdays' => $dayOfWeek >= 1 && $dayOfWeek <= 5,
            'weekends' => $dayOfWeek === 0 || $dayOfWeek === 6,
            'custom' => $habit->custom_days ? in_array($dayOfWeek, $habit->custom_days) : true,
            default => true,
        };
    }

    /**
     * Check if two dates are consecutive tracking days (considering frequency)
     * Returns 0 if consecutive, >0 if there are skipped tracking days
     */
    public function getExpectedDaysBetween(Habit $habit, Carbon $prevDate, Carbon $currentDate): int
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
     * Count total tracking days between two dates (respecting frequency)
     */
    public function countTrackingDays(Habit $habit, Carbon $startDate, Carbon $endDate): int
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
}
