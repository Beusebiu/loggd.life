<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Habit;
use App\Models\HabitCheck;
use App\Models\Category;
use App\Models\Entry;
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

        // Create categories
        $categories = [
            ['name' => 'Work', 'color' => '#3B82F6', 'icon' => '💼'],
            ['name' => 'Health', 'color' => '#10B981', 'icon' => '🏃'],
            ['name' => 'Personal', 'color' => '#8B5CF6', 'icon' => '✨'],
            ['name' => 'Learning', 'color' => '#F59E0B', 'icon' => '📚'],
            ['name' => 'Gratitude', 'color' => '#EC4899', 'icon' => '🙏'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $categoryData['name']],
                $categoryData
            );
        }

        $allCategories = $user->categories;

        // Create habits
        $habits = [
            [
                'name' => 'Morning Workout',
                'emoji' => '🏋️',
                'description' => '30 minutes of exercise',
                'frequency' => 'daily',
                'start_date' => now()->subDays(90),
                'color' => '#10B981',
            ],
            [
                'name' => 'Read Books',
                'emoji' => '📖',
                'description' => 'Read for at least 20 minutes',
                'frequency' => 'daily',
                'start_date' => now()->subDays(60),
                'color' => '#F59E0B',
            ],
            [
                'name' => 'Meditation',
                'emoji' => '🧘',
                'description' => '10 minutes of mindfulness',
                'frequency' => 'daily',
                'start_date' => now()->subDays(45),
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Learn Coding',
                'emoji' => '💻',
                'description' => 'Practice coding for 1 hour',
                'frequency' => 'weekdays',
                'start_date' => now()->subDays(30),
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Journal',
                'emoji' => '📝',
                'description' => 'Write in journal',
                'frequency' => 'daily',
                'start_date' => now()->subDays(20),
                'color' => '#EC4899',
            ],
        ];

        foreach ($habits as $habitData) {
            $habit = Habit::firstOrCreate(
                ['user_id' => $user->id, 'name' => $habitData['name']],
                $habitData
            );

            // Create habit checks for the past 90 days with realistic completion patterns
            for ($i = 90; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();

                // Skip weekends for "weekdays" habits
                if ($habitData['frequency'] === 'weekdays') {
                    $dayOfWeek = now()->subDays($i)->dayOfWeek;
                    if ($dayOfWeek === 0 || $dayOfWeek === 6) {
                        continue;
                    }
                }

                // Create realistic completion patterns (80-90% completion rate)
                $shouldComplete = rand(1, 100) <= 85;

                if ($shouldComplete) {
                    HabitCheck::firstOrCreate(
                        [
                            'habit_id' => $habit->id,
                            'date' => $date,
                        ],
                        [
                            'user_id' => $user->id,
                            'checked' => true,
                            'note' => $this->getRandomNote($habitData['name']),
                            'checked_at' => now()->subDays($i)->addHours(rand(6, 22)),
                        ]
                    );
                }
            }
        }

        // Create entries for the past 60 days
        $entryTemplates = [
            ['category' => 'Work', 'content' => 'Completed the new feature deployment. Team was really happy with the results!'],
            ['category' => 'Work', 'content' => 'Had a productive meeting with the product team. We aligned on Q1 priorities.'],
            ['category' => 'Health', 'content' => 'Felt amazing after today\'s workout. Hit a new personal record!'],
            ['category' => 'Health', 'content' => 'Meal prepped for the week. Feeling organized and healthy.'],
            ['category' => 'Personal', 'content' => 'Spent quality time with family. These moments matter most.'],
            ['category' => 'Personal', 'content' => 'Finally organized my workspace. Mental clarity follows physical clarity.'],
            ['category' => 'Learning', 'content' => 'Finished Chapter 5 of the Laravel book. Architecture patterns are fascinating!'],
            ['category' => 'Learning', 'content' => 'Watched a great tutorial on Vue composition API. Time to refactor!'],
            ['category' => 'Gratitude', 'content' => 'Grateful for supportive friends who always have my back.'],
            ['category' => 'Gratitude', 'content' => 'Thankful for good health and the ability to pursue my goals.'],
        ];

        for ($i = 60; $i >= 0; $i--) {
            $date = now()->subDays($i);

            // Create 1-3 entries per day (randomly)
            $entriesPerDay = rand(0, 3);

            for ($j = 0; $j < $entriesPerDay; $j++) {
                $template = $entryTemplates[array_rand($entryTemplates)];
                $category = $allCategories->where('name', $template['category'])->first();

                Entry::create([
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'content' => $template['content'],
                    'date' => $date->toDateString(),
                    'is_public' => rand(1, 100) <= 30, // 30% public
                    'created_at' => $date->addHours(rand(8, 22)),
                    'updated_at' => $date->addHours(rand(8, 22)),
                ]);
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Login: test@loggd.life / password123');
    }

    private function getRandomNote($habitName): ?string
    {
        $notes = [
            'Morning Workout' => [
                'Felt great today!',
                'Tough but worth it',
                '30 min cardio + weights',
                'New PR on bench press!',
                null,
            ],
            'Read Books' => [
                'Really enjoying this book',
                'Chapter 3 was insightful',
                'Can\'t put this down',
                '25 pages done',
                null,
            ],
            'Meditation' => [
                'Very peaceful session',
                'Mind was restless but pushed through',
                'Feeling centered',
                'Best session yet',
                null,
            ],
            'Learn Coding' => [
                'Learned about closures today',
                'Built a small project',
                'Finally understood async/await!',
                'LeetCode practice',
                null,
            ],
            'Journal' => [
                'Reflective morning',
                'Wrote about goals',
                'Processing recent events',
                'Gratitude journaling',
                null,
            ],
        ];

        if (isset($notes[$habitName])) {
            return $notes[$habitName][array_rand($notes[$habitName])];
        }

        return null;
    }
}
