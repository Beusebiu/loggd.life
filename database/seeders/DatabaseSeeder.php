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

        // Create habits with more variety
        $allHabits = [
            ['name' => 'Morning Workout', 'emoji' => '🏋️', 'description' => '30 minutes of exercise', 'frequency' => 'daily', 'days' => 90, 'color' => '#10B981'],
            ['name' => 'Read Books', 'emoji' => '📖', 'description' => 'Read for at least 20 minutes', 'frequency' => 'daily', 'days' => 60, 'color' => '#F59E0B'],
            ['name' => 'Meditation', 'emoji' => '🧘', 'description' => '10 minutes of mindfulness', 'frequency' => 'daily', 'days' => 45, 'color' => '#8B5CF6'],
            ['name' => 'Learn Coding', 'emoji' => '💻', 'description' => 'Practice coding for 1 hour', 'frequency' => 'weekdays', 'days' => 30, 'color' => '#3B82F6'],
            ['name' => 'Journal', 'emoji' => '📝', 'description' => 'Write in journal', 'frequency' => 'daily', 'days' => 20, 'color' => '#EC4899'],
            ['name' => 'Cold Shower', 'emoji' => '🚿', 'description' => 'Start the day with cold water', 'frequency' => 'daily', 'days' => 25, 'color' => '#06B6D4'],
            ['name' => 'Drink Water', 'emoji' => '💧', 'description' => 'Drink 2L throughout the day', 'frequency' => 'daily', 'days' => 80, 'color' => '#0EA5E9'],
            ['name' => 'Walk Outside', 'emoji' => '🚶', 'description' => '15 minute walk in nature', 'frequency' => 'daily', 'days' => 35, 'color' => '#22C55E'],
            ['name' => 'No Social Media', 'emoji' => '📵', 'description' => 'Avoid social media before noon', 'frequency' => 'weekdays', 'days' => 15, 'color' => '#EF4444'],
            ['name' => 'Meal Prep', 'emoji' => '🥗', 'description' => 'Prepare healthy meals', 'frequency' => 'weekends', 'days' => 40, 'color' => '#84CC16'],
            ['name' => 'Call Family', 'emoji' => '📞', 'description' => 'Check in with loved ones', 'frequency' => 'custom', 'days' => 50, 'color' => '#F472B6', 'custom_days' => [0, 3]],
            ['name' => 'Clean Workspace', 'emoji' => '🧹', 'description' => 'Tidy up desk and office', 'frequency' => 'weekends', 'days' => 60, 'color' => '#A78BFA'],
        ];

        // Randomly select 5-7 habits for variety
        $selectedCount = rand(5, 7);
        shuffle($allHabits);
        $habits = array_slice($allHabits, 0, $selectedCount);

        foreach ($habits as $habitData) {
            $startDays = $habitData['days'];
            unset($habitData['days']);

            $habit = Habit::firstOrCreate(
                ['user_id' => $user->id, 'name' => $habitData['name']],
                array_merge($habitData, ['start_date' => now()->subDays($startDays)])
            );

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

                // Varied completion rates based on habit age (more consistent over time)
                $daysActive = $startDays - $i;
                $completionRate = min(90, 60 + ($daysActive / 2)); // Starts at 60%, grows to 90%
                $shouldComplete = rand(1, 100) <= $completionRate;

                if ($shouldComplete) {
                    HabitCheck::firstOrCreate(
                        [
                            'habit_id' => $habit->id,
                            'date' => $date,
                        ],
                        [
                            'user_id' => $user->id,
                            'checked' => true,
                            'note' => rand(1, 100) <= 30 ? $this->getRandomNote($habitData['name']) : null, // 30% have notes
                            'checked_at' => $currentDate->addHours(rand(6, 22)),
                        ]
                    );
                }
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login: test@loggd.life / password123');

        // Seed Journey data (Vision, Goals, Weekly Reviews, Daily Check-ins)
        $this->call(JourneySeeder::class);

        // Create 50 comprehensive test profiles with everything
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
