<?php

namespace Database\Seeders;

use App\Models\Habit;
use App\Models\HabitCheck;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::firstOrCreate(
            ['email' => 'test@loggd.life'],
            [
                'name' => 'Test User',
                'username' => 'testuser',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
                'bio' => 'Personal growth enthusiast. Tracking my journey one day at a time.',
                'profile_public' => true,
                'timezone' => 'America/New_York',
            ]
        );

        // Create habits with more variety - some very old habits from 2023
        $allHabits = [
            ['name' => 'Morning Workout', 'emoji' => '🏋️', 'description' => '30 minutes of exercise', 'frequency' => 'daily', 'days' => 900, 'color' => '#10B981'], // ~2.5 years
            ['name' => 'Read Books', 'emoji' => '📖', 'description' => 'Read for at least 20 minutes', 'frequency' => 'daily', 'days' => 800, 'color' => '#F59E0B'], // ~2.2 years
            ['name' => 'Meditation', 'emoji' => '🧘', 'description' => '10 minutes of mindfulness', 'frequency' => 'daily', 'days' => 700, 'color' => '#8B5CF6'], // ~1.9 years
            ['name' => 'Learn Coding', 'emoji' => '💻', 'description' => 'Practice coding for 1 hour', 'frequency' => 'weekdays', 'days' => 600, 'color' => '#3B82F6'], // ~1.6 years
            ['name' => 'Journal', 'emoji' => '📝', 'description' => 'Write in journal', 'frequency' => 'daily', 'days' => 500, 'color' => '#EC4899'], // ~1.4 years
            ['name' => 'Cold Shower', 'emoji' => '🚿', 'description' => 'Start the day with cold water', 'frequency' => 'daily', 'days' => 200, 'color' => '#06B6D4'], // ~7 months
            ['name' => 'Drink Water', 'emoji' => '💧', 'description' => 'Drink 2L throughout the day', 'frequency' => 'daily', 'days' => 850, 'color' => '#0EA5E9'], // ~2.3 years
            ['name' => 'Walk Outside', 'emoji' => '🚶', 'description' => '15 minute walk in nature', 'frequency' => 'daily', 'days' => 400, 'color' => '#22C55E'], // ~1.1 years
            ['name' => 'No Social Media', 'emoji' => '📵', 'description' => 'Avoid social media before noon', 'frequency' => 'weekdays', 'days' => 150, 'color' => '#EF4444'], // ~5 months
            ['name' => 'Meal Prep', 'emoji' => '🥗', 'description' => 'Prepare healthy meals', 'frequency' => 'weekends', 'days' => 650, 'color' => '#84CC16'], // ~1.8 years
            ['name' => 'Call Family', 'emoji' => '📞', 'description' => 'Check in with loved ones', 'frequency' => 'custom', 'days' => 750, 'color' => '#F472B6', 'custom_days' => [0, 3]], // ~2 years
            ['name' => 'Clean Workspace', 'emoji' => '🧹', 'description' => 'Tidy up desk and office', 'frequency' => 'weekends', 'days' => 600, 'color' => '#A78BFA'], // ~1.6 years
        ];

        // Select 5-6 habits, prioritizing the oldest ones for historical data
        $selectedCount = rand(5, 6);
        // Take first few (oldest) habits to ensure good historical data
        $habits = array_slice($allHabits, 0, $selectedCount);

        foreach ($habits as $habitData) {
            $startDays = $habitData['days'];
            unset($habitData['days']);

            $habit = Habit::firstOrCreate(
                ['user_id' => $user->id, 'name' => $habitData['name']],
                array_merge($habitData, ['start_date' => now()->subDays($startDays)])
            );

            // Each habit gets a random base completion rate between 70-90%
            $baseCompletionRate = rand(70, 90);

            // Each habit has a preferred time of day
            $timeOfDayOptions = [
                ['morning' => [6, 9]],    // Morning habits
                ['midday' => [10, 14]],   // Midday habits
                ['evening' => [15, 19]],  // Evening habits
                ['night' => [19, 22]],    // Night habits
            ];
            $preferredTime = $timeOfDayOptions[array_rand($timeOfDayOptions)];
            $timeRange = reset($preferredTime);

            // Create habit checks with varied completion patterns
            for ($i = $startDays; $i >= 0; $i--) {
                $currentDate = now()->subDays($i);
                $date = $currentDate->toDateString();
                $dayOfWeek = $currentDate->dayOfWeek;

                // Skip based on frequency
                if ($habitData['frequency'] === 'weekdays' && ($dayOfWeek === 0 || $dayOfWeek === 6)) {
                    continue;
                }

                if ($habitData['frequency'] === 'weekends' && $dayOfWeek !== 0 && $dayOfWeek !== 6) {
                    continue;
                }

                if ($habitData['frequency'] === 'custom' && isset($habitData['custom_days']) && ! in_array($dayOfWeek, $habitData['custom_days'])) {
                    continue;
                }

                // Boost recent days (last 7 days) to ensure active streaks
                $isRecent = $i <= 7;

                // Add randomness: occasional "bad weeks" where completion drops
                $weekNumber = floor($i / 7);
                $isBadWeek = ($weekNumber % 8 === 0) && ! $isRecent; // Don't apply bad weeks to recent days
                $completionRate = $isBadWeek ? $baseCompletionRate - rand(15, 30) : $baseCompletionRate;

                // Recent days get a boost to maintain active streaks
                if ($isRecent) {
                    $completionRate = min(95, $completionRate + 15);
                } else {
                    // Add daily variation (+/- 10%) for older days
                    $completionRate += rand(-10, 10);
                }

                $completionRate = max(40, min(95, $completionRate)); // Keep between 40-95%

                $shouldComplete = rand(1, 100) <= $completionRate;

                if ($shouldComplete) {
                    // Add variation to time: 80% in preferred range, 20% outside
                    if (rand(1, 100) <= 80) {
                        $hour = rand($timeRange[0], $timeRange[1]);
                    } else {
                        $hour = rand(6, 22);
                    }

                    HabitCheck::firstOrCreate(
                        [
                            'habit_id' => $habit->id,
                            'date' => $date,
                        ],
                        [
                            'user_id' => $user->id,
                            'checked' => true,
                            'note' => rand(1, 100) <= 30 ? $this->getRandomNote($habitData['name']) : null, // 30% have notes
                            'checked_at' => $currentDate->setHour($hour)->setMinute(rand(0, 59)),
                        ]
                    );
                }
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login: test@loggd.life / password123');

        // Seed Journey data (Vision, Goals, Weekly Reviews, Daily Check-ins)
        $this->call(JourneySeeder::class);

        // Create 50 comprehensive test profiles with everything (including gamification)
        $this->call(ComprehensiveProfileSeeder::class);
    }

    private function getRandomNote($habitName): ?string
    {
        $notes = [
            'Morning Workout' => [
                'Felt great today!', 'Tough but worth it', '30 min cardio + weights', 'New PR!',
                'Legs are sore but good sore', 'Quick HIIT session', 'Yoga and stretching',
            ],
            'Read Books' => [
                'Really enjoying this book', 'Chapter 3 was insightful', 'Can\'t put this down',
                '25 pages done', 'Re-reading favorite parts', 'Finished another chapter',
            ],
            'Meditation' => [
                'Very peaceful session', 'Mind was restless but pushed through', 'Feeling centered',
                'Best session yet', 'Used Headspace app', '15 minutes of stillness', 'Breathing exercises',
            ],
            'Learn Coding' => [
                'Learned about closures today', 'Built a small project', 'Finally understood async/await!',
                'LeetCode practice', 'Refactored old code', 'New algorithm learned', 'Tutorial completed',
            ],
            'Journal' => [
                'Reflective morning', 'Wrote about goals', 'Processing recent events',
                'Gratitude journaling', 'Brain dump session', 'Planning tomorrow', 'Weekly review',
            ],
            'Cold Shower' => [
                'Energizing start!', 'Getting used to it', 'Tough but invigorating', '3 min cold blast',
            ],
            'Drink Water' => [
                '2L done!', 'Staying hydrated', 'Almost there', 'Tracking with app',
            ],
            'Walk Outside' => [
                'Beautiful weather', 'Needed the fresh air', '20 min nature walk', 'Cleared my head',
            ],
            'No Social Media' => [
                'Stayed strong!', 'More focused morning', 'Resisted the urge', 'Productive without distractions',
            ],
            'Meal Prep' => [
                'Lunches for the week ✓', 'Batch cooked', 'Healthy meals ready', 'Saved time and money',
            ],
            'Call Family' => [
                'Great chat with mom', 'Caught up with sibling', 'Video call with parents', 'Quick check-in',
            ],
            'Clean Workspace' => [
                'Desk organized', 'Feels much better', 'Minimalist setup', 'Fresh start',
            ],
        ];

        if (isset($notes[$habitName])) {
            return $notes[$habitName][array_rand($notes[$habitName])];
        }

        return null;
    }
}
