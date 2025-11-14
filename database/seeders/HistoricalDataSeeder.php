<?php

namespace Database\Seeders;

use App\Models\DailyCheckIn;
use App\Models\Goal;
use App\Models\GoalMilestone;
use App\Models\GoalUpdate;
use App\Models\Habit;
use App\Models\HabitCheck;
use App\Models\User;
use App\Models\Vision;
use App\Models\VisionSnapshot;
use App\Models\WeeklyReview;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HistoricalDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@loggd.life')->first();

        if (! $user) {
            $this->command->error('Test user not found. Run DatabaseSeeder first.');

            return;
        }

        $this->command->info('Creating historical data from 2023...');

        $this->seedVisionSnapshots($user);
        $this->seedHistoricalGoals($user);
        $this->seedHistoricalWeeklyReviews($user);
        $this->seedHistoricalDailyCheckIns($user);
        $this->seedHistoricalHabitChecks($user);

        $this->command->info('Historical data seeded successfully!');
    }

    private function seedVisionSnapshots(User $user): void
    {
        $snapshots = [
            [
                'snapshot_date' => '2023-01-01',
                'note' => 'New Year 2023 - Fresh start after a tough 2022. Ready to rebuild.',
                'eulogy_method' => "When I'm gone, I want to be remembered as someone who:\n\n- Never gave up, even when things were hard\n- Provided for family and kept them safe\n- Learned from mistakes and grew\n- Was reliable and trustworthy\n- Helped others when I could",
                'bucket_list' => [
                    ['text' => 'Start my own business', 'completed' => false],
                    ['text' => 'Learn web development properly', 'completed' => true],
                    ['text' => 'Take family on vacation abroad', 'completed' => false],
                    ['text' => 'Build emergency fund of €10k', 'completed' => false],
                    ['text' => 'Get back in shape', 'completed' => false],
                ],
                'mission_prompt' => 'To create financial stability for my family and prove I can build something valuable.',
            ],
            [
                'snapshot_date' => '2023-12-31',
                'note' => 'End of 2023 - Survived a challenging year. Made some progress but still a long way to go.',
                'eulogy_method' => "When I'm gone, I want to be remembered as someone who:\n\n- Fought through adversity and came out stronger\n- Provided for family and kept them safe\n- Built skills that opened new opportunities\n- Never stopped learning\n- Was present for important moments",
                'bucket_list' => [
                    ['text' => 'Start my own business', 'completed' => false],
                    ['text' => 'Learn web development properly', 'completed' => true],
                    ['text' => 'Take family on vacation abroad', 'completed' => false],
                    ['text' => 'Build emergency fund of €10k', 'completed' => false],
                    ['text' => 'Get back in shape', 'completed' => false],
                    ['text' => 'Get first freelance client', 'completed' => true],
                    ['text' => 'Build a web app from scratch', 'completed' => true],
                ],
                'mission_prompt' => 'To master software development and use it to create better opportunities for my family.',
            ],
            [
                'snapshot_date' => '2024-06-15',
                'note' => 'Mid-2024 check-in - Business is picking up. Contracting work stable. Feeling more confident.',
                'eulogy_method' => "When I'm gone, I want to be remembered as someone who:\n\n- Built something meaningful from scratch\n- Was always there for family\n- Helped others succeed\n- Took calculated risks and grew\n- Lived with purpose and intention",
                'bucket_list' => [
                    ['text' => 'Launch a successful business', 'completed' => false],
                    ['text' => 'Travel to Japan with family', 'completed' => false],
                    ['text' => 'Build emergency fund of €10k', 'completed' => true],
                    ['text' => 'Pay down mortgage by €20k', 'completed' => false],
                    ['text' => 'Build a dream garage workshop', 'completed' => false],
                    ['text' => 'Speak at a tech event', 'completed' => false],
                    ['text' => 'Cycle 400km in a year', 'completed' => false],
                ],
                'mission_prompt' => 'To build products that help people achieve their goals while maintaining balance with family.',
            ],
            [
                'snapshot_date' => '2024-12-31',
                'note' => 'End of 2024 - Best year yet! Stable income, growing savings, clear vision for 2025.',
                'eulogy_method' => "When I'm gone, I want to be remembered as someone who:\n\n- Built something meaningful that helped others\n- Was present and engaged with family\n- Never stopped learning and improving\n- Took calculated risks and succeeded\n- Inspired others to chase their dreams",
                'bucket_list' => [
                    ['text' => 'Launch a successful SaaS business', 'completed' => false],
                    ['text' => 'Travel to Japan with family', 'completed' => false],
                    ['text' => 'Build a dream garage workshop', 'completed' => false],
                    ['text' => 'Restore a classic car project', 'completed' => false],
                    ['text' => 'Speak at a tech conference', 'completed' => false],
                    ['text' => 'Pay off €20k of mortgage', 'completed' => true],
                    ['text' => 'Cycle 400km in a year', 'completed' => true],
                    ['text' => 'Build €15k emergency fund', 'completed' => true],
                ],
                'mission_prompt' => 'To build products that help people achieve more while maintaining a balanced life with family.',
            ],
        ];

        foreach ($snapshots as $snapshot) {
            VisionSnapshot::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'snapshot_date' => $snapshot['snapshot_date'],
                ],
                array_merge($snapshot, ['is_public' => false])
            );
        }

        $this->command->info('Vision snapshots created: '.count($snapshots));
    }

    private function seedHistoricalGoals(User $user): void
    {
        // Archived/completed goals from 2023-2024
        $archivedGoals = [
            [
                'title' => 'Build emergency fund of €10,000',
                'time_horizon' => 'yearly',
                'life_area' => 'financial',
                'tracking_type' => 'metric',
                'metric_unit' => '€',
                'metric_start_value' => 0,
                'metric_target_value' => 10000,
                'metric_current_value' => 10000,
                'status' => 'completed',
                'started_at' => '2023-01-01',
                'target_date' => '2023-12-31',
                'completed_at' => '2023-11-15',
                'period_identifier' => '2023',
            ],
            [
                'title' => 'Get first 5 freelance clients',
                'time_horizon' => 'yearly',
                'life_area' => 'career',
                'tracking_type' => 'metric',
                'metric_unit' => '#',
                'metric_start_value' => 0,
                'metric_target_value' => 5,
                'metric_current_value' => 7,
                'status' => 'completed',
                'started_at' => '2023-01-01',
                'target_date' => '2023-12-31',
                'completed_at' => '2023-10-20',
                'period_identifier' => '2023',
            ],
            [
                'title' => 'Learn React and TypeScript',
                'time_horizon' => 'quarterly',
                'life_area' => 'growth',
                'tracking_type' => 'milestone',
                'status' => 'completed',
                'started_at' => '2023-01-01',
                'target_date' => '2023-03-31',
                'completed_at' => '2023-03-28',
                'period_identifier' => 'Q1_2023',
            ],
            [
                'title' => 'Pay down €20k of mortgage',
                'time_horizon' => 'yearly',
                'life_area' => 'financial',
                'tracking_type' => 'metric',
                'metric_unit' => '€',
                'metric_start_value' => 0,
                'metric_target_value' => 20000,
                'metric_current_value' => 22000,
                'metric_decrease' => false,
                'status' => 'completed',
                'started_at' => '2023-01-01',
                'target_date' => '2024-12-31',
                'completed_at' => '2024-11-30',
                'period_identifier' => '2023-2024',
            ],
            [
                'title' => 'Cycle 400km throughout the year',
                'time_horizon' => 'yearly',
                'life_area' => 'health',
                'tracking_type' => 'metric',
                'metric_unit' => 'km',
                'metric_start_value' => 0,
                'metric_target_value' => 400,
                'metric_current_value' => 425,
                'status' => 'completed',
                'started_at' => '2024-01-01',
                'target_date' => '2024-12-31',
                'completed_at' => '2024-12-15',
                'period_identifier' => '2024',
            ],
            [
                'title' => 'Build portfolio website',
                'time_horizon' => 'monthly',
                'life_area' => 'career',
                'tracking_type' => 'milestone',
                'status' => 'completed',
                'started_at' => '2023-02-01',
                'target_date' => '2023-02-28',
                'completed_at' => '2023-02-25',
                'period_identifier' => 'Feb_2023',
            ],
            [
                'title' => 'Launch first SaaS product (abandoned)',
                'time_horizon' => 'quarterly',
                'life_area' => 'career',
                'tracking_type' => 'evolution',
                'status' => 'paused',
                'started_at' => '2023-07-01',
                'target_date' => '2023-09-30',
                'period_identifier' => 'Q3_2023',
                'last_update_note' => 'Pivoted away after market research showed insufficient demand. Learned valuable lessons about validation.',
            ],
        ];

        foreach ($archivedGoals as $goalData) {
            Goal::firstOrCreate(
                ['user_id' => $user->id, 'title' => $goalData['title']],
                array_merge($goalData, ['is_public' => false])
            );
        }

        $this->command->info('Archived goals created: '.count($archivedGoals));
    }

    private function seedHistoricalWeeklyReviews(User $user): void
    {
        $reviews = [];
        $startDate = Carbon::parse('2023-01-02'); // First Monday of 2023
        $endDate = Carbon::parse('2025-09-01'); // Stop before current seeded data

        $current = $startDate->copy();
        $weekNumber = 1;

        $winTemplates = [
            'Finished major feature for client project',
            'Launched new portfolio piece',
            'Had productive week on personal project',
            'Got positive feedback from client',
            'Shipped important bug fix',
            'Completed learning module on new tech',
            'Made breakthrough on hard problem',
            'Organized workspace and improved workflow',
            'Reached important milestone',
            'Had great family time while staying productive',
            'Closed a new contract',
            'Built new feature I\'m proud of',
            'Fixed technical debt that was bothering me',
            'Improved test coverage significantly',
            'Optimized performance on key feature',
        ];

        $challengeTemplates = [
            'Struggled with motivation mid-week',
            'Client changed requirements unexpectedly',
            'Hit technical roadblock that took time to solve',
            'Felt overwhelmed by todo list',
            'Balancing client work with own projects was hard',
            'Low energy after long work days',
            'Distracted by non-essential tasks',
            'Perfectionism slowed me down',
            'Difficult conversation with client',
            'Context switching killed productivity',
            'Burnout feeling creeping in',
            'Imposter syndrome this week',
            'Time estimation way off again',
            'Too much time in meetings',
            'Felt stuck on technical problem',
        ];

        $learningTemplates = [
            'Learned new TypeScript pattern that will help future work',
            'Realized I work best in morning deep work blocks',
            'Shipping something imperfect is better than not shipping',
            'Need to set clearer boundaries with clients',
            'Breaking big tasks into smaller ones helps momentum',
            'Taking breaks actually improves productivity',
            'Documentation saves time in the long run',
            'Asking for help earlier saves hours of frustration',
            'Regular exercise significantly improves focus',
            'Planning the week on Sunday sets me up for success',
            'Saying no to good opportunities creates space for great ones',
            'Consistency beats intensity every time',
            'Small daily progress compounds surprisingly fast',
            'Working on products I use myself is more motivating',
            'Competition validates the market',
        ];

        $focusTemplates = [
            "1. Ship remaining features for client project\n2. Start planning next quarter goals\n3. Family weekend adventure",
            "1. Complete technical documentation\n2. Review and refactor messy code\n3. One day completely off",
            "1. Launch new product feature\n2. Get user feedback and iterate\n3. Plan Q2 strategy",
            "1. Deep work on core product development\n2. No meetings - protect focus time\n3. Exercise 3x this week",
            "1. Close current client project\n2. Line up next contract\n3. Review financial goals",
        ];

        while ($current->lessThan($endDate)) {
            $weekEnd = $current->copy()->addDays(6);

            // Vary the data to make it realistic
            $visionAlignment = rand(4, 9);
            $totalCheckIns = rand(2, 7);
            $avgMood = rand(50, 85) / 10;
            $avgEnergy = rand(45, 80) / 10;
            $avgProductivity = rand(50, 85) / 10;

            // Not every week has complete data
            $hasData = rand(1, 100) > 15; // 85% of weeks have data

            if ($hasData) {
                $reviews[] = [
                    'week_start_date' => $current->format('Y-m-d'),
                    'week_end_date' => $weekEnd->format('Y-m-d'),
                    'biggest_win' => $winTemplates[array_rand($winTemplates)],
                    'biggest_challenge' => $challengeTemplates[array_rand($challengeTemplates)],
                    'what_i_learned' => $learningTemplates[array_rand($learningTemplates)],
                    'vision_alignment' => $visionAlignment,
                    'next_week_focus' => $focusTemplates[array_rand($focusTemplates)],
                    'total_check_ins' => $totalCheckIns,
                    'avg_energy' => $avgEnergy,
                    'avg_productivity' => $avgProductivity,
                    'avg_mood' => $avgMood,
                ];
            }

            $current->addWeeks(1);
            $weekNumber++;
        }

        // Bulk insert for performance
        foreach (array_chunk($reviews, 50) as $chunk) {
            foreach ($chunk as $review) {
                WeeklyReview::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'week_start_date' => $review['week_start_date'],
                    ],
                    array_merge($review, ['is_public' => false])
                );
            }
        }

        $this->command->info('Historical weekly reviews created: '.count($reviews));
    }

    private function seedHistoricalDailyCheckIns(User $user): void
    {
        $checkIns = [];
        $startDate = Carbon::parse('2023-01-01');
        $endDate = Carbon::parse('2025-09-01'); // Stop before current seeded data

        $priorities = [
            'Focus on client deliverables',
            'Work on personal project',
            'Deep work session - no distractions',
            'Admin and planning day',
            'Learning and skill development',
            'Content creation and marketing',
            'Code review and refactoring',
            'Research and planning',
            'Meetings and collaboration',
            'Ship something small but complete',
        ];

        $reflections = [
            "✅ Productive coding session\n✅ Good client call\n\n🤔 Low energy in afternoon\n\n🙏 Grateful for steady work",
            "✅ Shipped new feature\n✅ Got positive feedback\n\n🤔 Took longer than expected\n\n🙏 Thankful for supportive family",
            "✅ Solved hard bug\n✅ Learned new concept\n\n🤔 Distracted by social media\n\n🙏 Appreciate the journey",
            "✅ Good exercise session\n✅ Healthy eating\n\n🤔 Work dragged on\n\n🙏 Grateful for health",
            "✅ Quality family time\n✅ Relaxed evening\n\n🤔 Didn't work on goals\n\n🙏 Thankful for perspective",
            "✅ Made progress on project\n✅ Organized workspace\n\n🤔 Overwhelmed by todo list\n\n🙏 Grateful for opportunities",
            "✅ Finished important task\n✅ Helped colleague\n\n🤔 Perfectionism slowed me\n\n🙏 Thankful for growth",
            "✅ Great deep work block\n✅ No interruptions\n\n🤔 Tired in evening\n\n🙏 Appreciate focused time",
            "✅ Valuable conversation\n✅ Got feedback\n\n🤔 Unclear on next steps\n\n🙏 Grateful for mentors",
            "✅ Small win on project\n✅ Consistent progress\n\n🤔 Still lots to do\n\n🙏 Thankful for persistence",
        ];

        $current = $startDate->copy();

        while ($current->lessThan($endDate)) {
            // Not every day has a check-in (about 60% coverage)
            if (rand(1, 100) <= 60) {
                $checkIns[] = [
                    'date' => $current->format('Y-m-d'),
                    'today_priority' => $priorities[array_rand($priorities)],
                    'day_reflection' => $reflections[array_rand($reflections)],
                    'mood' => rand(5, 8),
                ];
            }

            $current->addDay();
        }

        // Bulk insert for performance
        foreach (array_chunk($checkIns, 100) as $chunk) {
            foreach ($chunk as $checkIn) {
                DailyCheckIn::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'date' => $checkIn['date'],
                    ],
                    array_merge($checkIn, [
                        'today_tasks' => [],
                        'goals_worked_on' => [],
                        'is_public' => false,
                    ])
                );
            }
        }

        $this->command->info('Historical daily check-ins created: '.count($checkIns));
    }

    private function seedHistoricalHabitChecks(User $user): void
    {
        // Get user's habits
        $habits = Habit::where('user_id', $user->id)->get();

        if ($habits->isEmpty()) {
            $this->command->warn('No habits found for user. Skipping habit check seeding.');
            return;
        }

        $totalChecks = 0;

        foreach ($habits as $habit) {
            $checks = [];

            // Start from habit creation or 2023, whichever is later
            $startDate = Carbon::parse($habit->start_date);
            if ($startDate->year < 2023) {
                $startDate = Carbon::parse('2023-01-01');
            }

            $endDate = Carbon::parse('2025-09-01'); // Stop before current data
            $current = $startDate->copy();

            // Determine completion rate based on habit consistency simulation
            // Early days: lower rate (60-70%), recent days: higher rate (75-85%)
            while ($current->lessThan($endDate)) {
                // Only process days that match habit frequency
                $shouldTrackToday = $this->shouldTrackHabitDay($habit, $current);

                if ($shouldTrackToday) {
                    // Calculate completion rate that improves over time
                    $daysSinceStart = $startDate->diffInDays($current);
                    $improvementFactor = min(20, $daysSinceStart / 30); // Caps at 20% improvement
                    $baseRate = 60; // Start at 60%
                    $completionRate = $baseRate + $improvementFactor;

                    if (rand(1, 100) <= $completionRate) {
                        $checks[] = [
                            'habit_id' => $habit->id,
                            'user_id' => $user->id,
                            'date' => $current->format('Y-m-d'),
                            'checked' => true,
                            'checked_at' => $current->copy()->addHours(rand(6, 22))->addMinutes(rand(0, 59)),
                            'created_at' => $current->copy()->addHours(rand(6, 22)),
                            'updated_at' => $current->copy()->addHours(rand(6, 22)),
                        ];
                    }
                }

                $current->addDay();
            }

            // Bulk insert for performance
            foreach (array_chunk($checks, 100) as $chunk) {
                HabitCheck::insert($chunk);
            }

            $totalChecks += count($checks);
        }

        $this->command->info("Historical habit checks created: {$totalChecks} across {$habits->count()} habits");
    }

    private function shouldTrackHabitDay(Habit $habit, Carbon $date): bool
    {
        $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday

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
                if (!$habit->custom_days) {
                    return true; // Default to true if custom_days not set
                }
                return in_array($dayOfWeek, $habit->custom_days);

            default:
                return true;
        }
    }
}
