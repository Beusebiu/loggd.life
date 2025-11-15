<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Daily Check-In Configuration
    |--------------------------------------------------------------------------
    */
    'daily_check_in' => [
        'mood_scale' => [
            'min' => 1,
            'max' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Weekly Review Configuration
    |--------------------------------------------------------------------------
    */
    'weekly_review' => [
        'vision_alignment_scale' => [
            'min' => 1,
            'max' => 10,
        ],
        'max_goal_progress_length' => 1000,
        'max_next_week_goal_length' => 500,
        'lookback_days' => 30, // For recent wins
    ],

    /*
    |--------------------------------------------------------------------------
    | Goal Configuration
    |--------------------------------------------------------------------------
    */
    'goals' => [
        'time_horizons' => [
            '3_year',
            'yearly',
            'quarterly',
            'monthly',
        ],
        'life_areas' => [
            'career',
            'health',
            'relationships',
            'financial',
            'growth',
            'impact',
            'other',
        ],
        'tracking_types' => [
            'metric',
            'milestone',
            'evolution',
            'active',
        ],
        'statuses' => [
            'active',
            'completed',
            'paused',
            'abandoned',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Habit Configuration
    |--------------------------------------------------------------------------
    */
    'habits' => [
        'frequencies' => [
            'daily',
            'weekdays',
            'weekends',
            'custom',
        ],
        'statuses' => [
            'active',
            'archived',
        ],
        'contribution_grid_days' => 365,
        'max_streak_calculation_days' => 365,
    ],
];
