<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Points Configuration
    |--------------------------------------------------------------------------
    */
    'points' => [
        // Base points for each activity type (defined in ActivityType enum)
        'multipliers' => [
            'same_day' => 1.0,          // 100% points for same-day activities
            'within_7_days' => 0.5,     // 50% points for backfill within 7 days
            'older_than_7_days' => 0.0, // 0% points for older backfills
        ],

        // Weekend and special day bonuses
        'time_multipliers' => [
            'friday' => 1.5,
            'saturday' => 1.5,
            'sunday' => 2.0,
            'weekday' => 1.0,
        ],

        // Comeback multiplier for returning users
        'comeback' => [
            'multiplier' => 3.0,
            'inactive_days_threshold' => 7,
            'duration_hours' => 24,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Levels and Tiers
    |--------------------------------------------------------------------------
    */
    'levels' => [
        'points_per_level' => 100,
        'progression_exponent' => 1.8,

        'tiers' => [
            'beginner' => ['start' => 1, 'end' => 5],
            'explorer' => ['start' => 6, 'end' => 15],
            'achiever' => ['start' => 16, 'end' => 30],
            'master' => ['start' => 31, 'end' => 50],
            'legend' => ['start' => 51, 'end' => PHP_INT_MAX],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Streaks Configuration
    |--------------------------------------------------------------------------
    */
    'streaks' => [
        'max_gap_days' => 1, // Maximum gap before streak is broken
        'max_calculation_days' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Grid
    |--------------------------------------------------------------------------
    */
    'activity_grid' => [
        'intensity_levels' => [
            0 => ['min' => 0, 'max' => 0],      // No activity
            1 => ['min' => 1, 'max' => 5],      // Low
            2 => ['min' => 6, 'max' => 10],     // Medium
            3 => ['min' => 11, 'max' => 15],    // High
            4 => ['min' => 16, 'max' => PHP_INT_MAX], // Very High
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Leaderboard Configuration
    |--------------------------------------------------------------------------
    */
    'leaderboard' => [
        'global_limit' => 100,
        'cache_duration_minutes' => 15,
        'week_start_day' => 'monday',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Editing
    |--------------------------------------------------------------------------
    */
    'activity' => [
        'edit_window_days' => 7, // Users can edit activities within 7 days
    ],
];
