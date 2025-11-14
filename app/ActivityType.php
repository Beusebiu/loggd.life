<?php

namespace App;

enum ActivityType: string
{
    case HabitCheck = 'habit_check';
    case DailyReflection = 'daily_reflection';
    case WeeklyReview = 'weekly_review';
    case GoalMilestone = 'goal_milestone';
    case GoalUpdate = 'goal_update';
    case VisionUpdate = 'vision_update';

    /**
     * Get base points for this activity type (100% for same-day)
     */
    public function basePoints(): int
    {
        return match ($this) {
            self::HabitCheck => 2,
            self::DailyReflection => 10,
            self::WeeklyReview => 20,
            self::GoalMilestone => 15,
            self::GoalUpdate => 5,
            self::VisionUpdate => 10,
        };
    }

    /**
     * Get display label for this activity type
     */
    public function label(): string
    {
        return match ($this) {
            self::HabitCheck => 'Habit Check',
            self::DailyReflection => 'Daily Reflection',
            self::WeeklyReview => 'Weekly Review',
            self::GoalMilestone => 'Goal Milestone',
            self::GoalUpdate => 'Goal Update',
            self::VisionUpdate => 'Vision Update',
        };
    }
}
