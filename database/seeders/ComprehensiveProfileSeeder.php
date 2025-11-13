<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\DailyCheckIn;
use App\Models\Goal;
use App\Models\GoalMetric;
use App\Models\GoalMilestone;
use App\Models\GoalTask;
use App\Models\Habit;
use App\Models\HabitCheck;
use App\Models\User;
use App\Models\Vision;
use App\Models\WeeklyReview;
use Illuminate\Database\Seeder;

class ComprehensiveProfileSeeder extends Seeder
{
    private $habitTemplates = [
        ['name' => 'Morning Workout', 'emoji' => '🏋️', 'color' => '#ef4444'],
        ['name' => 'Read 30 Minutes', 'emoji' => '📚', 'color' => '#3b82f6'],
        ['name' => 'Meditate', 'emoji' => '🧘', 'color' => '#8b5cf6'],
        ['name' => 'Drink Water', 'emoji' => '💧', 'color' => '#06b6d4'],
        ['name' => 'Write Journal', 'emoji' => '📝', 'color' => '#f59e0b'],
        ['name' => 'No Social Media', 'emoji' => '🚫', 'color' => '#ef4444'],
        ['name' => 'Practice Guitar', 'emoji' => '🎸', 'color' => '#ec4899'],
        ['name' => 'Code Side Project', 'emoji' => '💻', 'color' => '#10b981'],
        ['name' => 'Cold Shower', 'emoji' => '🚿', 'color' => '#06b6d4'],
        ['name' => 'Study Language', 'emoji' => '🗣️', 'color' => '#f59e0b'],
        ['name' => 'Walk 10k Steps', 'emoji' => '🚶', 'color' => '#84cc16'],
        ['name' => 'Stretch Routine', 'emoji' => '🤸', 'color' => '#a855f7'],
        ['name' => 'Healthy Breakfast', 'emoji' => '🥗', 'color' => '#22c55e'],
        ['name' => 'Plan Tomorrow', 'emoji' => '📅', 'color' => '#6366f1'],
        ['name' => 'Gratitude Practice', 'emoji' => '🙏', 'color' => '#f97316'],
    ];

    private $goalTemplates = [
        ['title' => 'Launch SaaS Business', 'life_area' => 'career', 'time_horizon' => '3_year'],
        ['title' => 'Read 24 Books This Year', 'life_area' => 'growth', 'time_horizon' => 'yearly'],
        ['title' => 'Run a Marathon', 'life_area' => 'health', 'time_horizon' => 'yearly'],
        ['title' => 'Learn Spanish Fluently', 'life_area' => 'growth', 'time_horizon' => '3_year'],
        ['title' => 'Save $50k Emergency Fund', 'life_area' => 'financial', 'time_horizon' => '3_year'],
        ['title' => 'Build Dream Home Office', 'life_area' => 'other', 'time_horizon' => 'yearly'],
        ['title' => 'Complete Online Course', 'life_area' => 'growth', 'time_horizon' => 'quarterly'],
        ['title' => 'Lose 20 Pounds', 'life_area' => 'health', 'time_horizon' => 'quarterly'],
        ['title' => 'Travel to Japan', 'life_area' => 'other', 'time_horizon' => 'yearly'],
        ['title' => 'Write a Book', 'life_area' => 'career', 'time_horizon' => '3_year'],
    ];

    public function run(): void
    {
        $this->command->info('Creating 50 diverse test profiles...');

        for ($i = 1; $i <= 50; $i++) {
            $this->command->info("Creating profile {$i}/50...");

            $user = User::create([
                'name' => fake()->name(),
                'email' => "testuser{$i}@loggd.test",
                'username' => "user{$i}",
                'password' => bcrypt('password'),
                'bio' => $this->getRandomBio(),
                'profile_public' => fake()->boolean(70), // 70% public profiles
                'timezone' => fake()->randomElement(['America/New_York', 'Europe/London', 'Asia/Tokyo', 'America/Los_Angeles']),
                'notification_style' => fake()->randomElement(['polite', 'raw']),
                'created_at' => now()->subDays(rand(30, 365)),
            ]);

            // Create habits (3-10 per user)
            $this->createHabits($user, rand(3, 10));

            // Create goals (2-8 per user)
            $this->createGoals($user, rand(2, 8));

            // Create vision (80% chance)
            if (fake()->boolean(80)) {
                $this->createVision($user);
            }

            // Create achievements (1-5 per user)
            $this->createAchievements($user, rand(1, 5));
        }

        $this->command->info('✅ Created 50 diverse test profiles successfully!');
    }

    private function createHabits(User $user, int $count): void
    {
        $templates = fake()->randomElements($this->habitTemplates, $count);
        $frequencies = ['daily', 'weekdays', 'weekends', 'custom'];

        foreach ($templates as $template) {
            $frequency = fake()->randomElement($frequencies);
            $startDate = now()->subDays(rand(7, 180));
            $isActive = fake()->boolean(85); // 85% active, 15% archived

            $allowMultiple = fake()->boolean(30); // 30% allow multiple checks

            $habit = Habit::create([
                'user_id' => $user->id,
                'name' => $template['name'],
                'emoji' => $template['emoji'],
                'color' => $template['color'],
                'frequency' => $frequency,
                'custom_days' => $frequency === 'custom' ? [1, 3, 5] : null, // Mon, Wed, Fri
                'start_date' => $startDate,
                'status' => $isActive ? 'active' : 'archived',
                'is_public' => fake()->boolean(60), // 60% public
                'allow_multiple_checks' => $allowMultiple,
            ]);

            // Create habit checks (historical data)
            if ($isActive) {
                $this->createHabitChecks($user, $habit, $startDate);
            }
        }
    }

    private function createHabitChecks(User $user, Habit $habit, $startDate): void
    {
        $currentDate = $startDate->copy();
        $today = now();
        $completionRate = fake()->randomFloat(2, 0.5, 0.95); // 50-95% completion

        while ($currentDate->lte($today)) {
            $dayOfWeek = $currentDate->dayOfWeek;
            $shouldTrack = $this->shouldTrackDay($habit, $dayOfWeek);

            if ($shouldTrack) {
                $completed = fake()->boolean($completionRate * 100);

                if ($completed) {
                    // If habit allows multiple checks, create 1-5 checks per day
                    $checksCount = $habit->allow_multiple_checks ? rand(1, 5) : 1;

                    for ($i = 0; $i < $checksCount; $i++) {
                        HabitCheck::create([
                            'user_id' => $user->id,
                            'habit_id' => $habit->id,
                            'date' => $currentDate->toDateString(),
                            'checked' => true,
                        ]);
                    }
                }
            }

            $currentDate->addDay();
        }
    }

    private function shouldTrackDay(Habit $habit, int $dayOfWeek): bool
    {
        return match ($habit->frequency) {
            'daily' => true,
            'weekdays' => $dayOfWeek >= 1 && $dayOfWeek <= 5,
            'weekends' => $dayOfWeek === 0 || $dayOfWeek === 6,
            'custom' => in_array($dayOfWeek, $habit->custom_days ?? []),
            default => true,
        };
    }

    private function createGoals(User $user, int $count): void
    {
        $templates = fake()->randomElements($this->goalTemplates, $count);

        foreach ($templates as $template) {
            $status = fake()->randomElement(['active', 'active', 'active', 'completed']); // 75% active
            $trackingType = fake()->randomElement(['milestone', 'metric', 'evolution']);
            $targetDate = $this->getTargetDate($template['time_horizon'], $status);

            $goalData = [
                'user_id' => $user->id,
                'title' => $template['title'],
                'description' => fake()->sentence(10),
                'time_horizon' => $template['time_horizon'],
                'life_area' => $template['life_area'],
                'tracking_type' => $trackingType,
                'target_date' => $targetDate,
                'status' => $status,
                'is_public' => fake()->boolean(70),
                'created_at' => now()->subDays(rand(30, 365)),
            ];

            // Add metric fields for metric-based goals
            if ($trackingType === 'metric') {
                $targetValue = rand(10, 100);
                $currentValue = $status === 'completed' ? $targetValue : rand(0, $targetValue);

                $goalData['metric_unit'] = fake()->randomElement(['€', 'kg', 'books', 'days', 'km']);
                $goalData['metric_start_value'] = 0;
                $goalData['metric_target_value'] = $targetValue;
                $goalData['metric_current_value'] = $currentValue;
                $goalData['metric_decrease'] = fake()->boolean(20); // 20% are decrease goals (like mortgage)
            }

            $goal = Goal::create($goalData);

            // Create milestones for milestone-based goals
            if ($trackingType === 'milestone') {
                $this->createMilestones($goal, rand(3, 7), $status);
            }

            // Create tasks
            $this->createTasks($goal, rand(2, 8), $status);
        }
    }

    private function createMilestones(Goal $goal, int $count, string $status): void
    {
        $milestoneNames = [
            'Research and Planning',
            'Initial Setup',
            'First Prototype',
            'Beta Testing',
            'Launch Preparation',
            'Public Launch',
            'First Customer',
            'Break Even',
            'Scale Operations',
        ];

        $names = fake()->randomElements($milestoneNames, $count);
        $completedCount = $status === 'completed' ? $count : rand(0, $count - 1);

        foreach ($names as $index => $name) {
            $completed = $index < $completedCount;

            GoalMilestone::create([
                'goal_id' => $goal->id,
                'title' => $name,
                'completed' => $completed,
                'completed_at' => $completed ? now()->subDays(rand(1, 90)) : null,
                'order' => $index,
            ]);
        }
    }

    private function createTasks(Goal $goal, int $count, string $status): void
    {
        $taskTemplates = [
            'Research options',
            'Create action plan',
            'Set up tools',
            'Complete first draft',
            'Get feedback',
            'Make revisions',
            'Finalize details',
            'Review and test',
        ];

        $tasks = fake()->randomElements($taskTemplates, $count);
        $completedCount = $status === 'completed' ? $count : rand(0, $count - 1);

        foreach ($tasks as $index => $task) {
            $completed = $index < $completedCount;

            GoalTask::create([
                'goal_id' => $goal->id,
                'title' => $task,
                'completed' => $completed,
                'completed_at' => $completed ? now()->subDays(rand(1, 60)) : null,
                'order' => $index,
            ]);
        }
    }

    private function createVision(User $user): void
    {
        $privateSections = fake()->randomElements(
            ['mission_prompt', 'eulogy_method', 'bucket_list', 'definition_of_success', 'odyssey_plan', 'future_calendar'],
            rand(0, 2)
        );

        Vision::create([
            'user_id' => $user->id,
            'mission_prompt' => $this->getRandomMission(),
            'eulogy_method' => $this->getRandomEulogy(),
            'bucket_list' => $this->getRandomBucketList(),
            'definition_of_success' => $this->getRandomDefinitionOfSuccess(),
            'odyssey_plan' => $this->getRandomOdysseyPlan(),
            'future_calendar' => $this->getRandomFutureCalendar(),
            'is_public' => fake()->boolean(60),
            'private_sections' => $privateSections,
        ]);
    }

    private function createDailyCheckIns(User $user): void
    {
        $days = rand(10, 30); // Random 10-30 days of check-ins

        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i);

            DailyCheckIn::create([
                'user_id' => $user->id,
                'date' => $date->toDateString(),
                'morning_intention' => fake()->sentence(8),
                'top_priorities' => [
                    fake()->sentence(4),
                    fake()->sentence(4),
                    fake()->sentence(4),
                ],
                'evening_gratitude' => fake()->randomElement([fake()->sentence(8), null]),
                'evening_wins' => fake()->boolean(70) ? [fake()->sentence(5), fake()->sentence(5)] : null,
                'evening_lessons' => fake()->boolean(60) ? fake()->sentence(10) : null,
                'mood_rating' => rand(1, 5),
                'energy_level' => rand(1, 5),
                'notes' => fake()->boolean(40) ? fake()->paragraph(2) : null,
            ]);
        }
    }

    private function createWeeklyReviews(User $user): void
    {
        $weeks = rand(4, 8); // Random 4-8 weeks of reviews

        for ($i = 0; $i < $weeks; $i++) {
            $weekStart = now()->subWeeks($i)->startOfWeek();

            WeeklyReview::create([
                'user_id' => $user->id,
                'week_start' => $weekStart->toDateString(),
                'wins' => [
                    fake()->sentence(6),
                    fake()->sentence(6),
                    fake()->sentence(6),
                ],
                'challenges' => [
                    fake()->sentence(6),
                    fake()->sentence(6),
                ],
                'lessons_learned' => fake()->paragraph(2),
                'next_week_focus' => [
                    fake()->sentence(5),
                    fake()->sentence(5),
                    fake()->sentence(5),
                ],
                'overall_rating' => rand(1, 5),
                'notes' => fake()->boolean(50) ? fake()->paragraph(2) : null,
            ]);
        }
    }

    private function createAchievements(User $user, int $count): void
    {
        $achievementTypes = [
            ['type' => 'streak_7', 'metadata' => ['streak_count' => 7]],
            ['type' => 'streak_30', 'metadata' => ['streak_count' => 30]],
            ['type' => 'goal_completed', 'metadata' => ['goal_id' => null]],
            ['type' => 'habit_completed', 'metadata' => ['habit_id' => null]],
            ['type' => 'streak_100', 'metadata' => ['streak_count' => 100]],
            ['type' => 'perfect_week', 'metadata' => []],
        ];

        $achievements = fake()->randomElements($achievementTypes, $count);

        foreach ($achievements as $achievement) {
            Achievement::create([
                'user_id' => $user->id,
                'achievement_type' => $achievement['type'],
                'metadata' => $achievement['metadata'],
                'shown_at' => now()->subDays(rand(0, 5)),
                'created_at' => now()->subDays(rand(1, 180)),
            ]);
        }
    }

    private function getTargetDate(string $timeHorizon, string $status): string
    {
        $daysToAdd = match ($timeHorizon) {
            'monthly' => rand(10, 30),
            'quarterly' => rand(30, 90),
            'yearly' => rand(180, 365),
            '3_year' => rand(730, 1095),
            default => 365,
        };

        if ($status === 'completed') {
            return now()->subDays(rand(1, 30))->toDateString();
        }

        return now()->addDays($daysToAdd)->toDateString();
    }

    private function getRandomBio(): string
    {
        $bios = [
            'Building a life of consistency and growth 🚀',
            'One day at a time. One habit at a time.',
            'Tracking my journey to becoming my best self',
            'Entrepreneur | Developer | Lifelong Learner',
            'Making progress every single day 💪',
            'Building in public. Growing in private.',
            'Focused on health, wealth, and wisdom',
            'Dad. Coder. Runner. Reader.',
            '365 days of growth and discipline',
            'Here to prove consistency beats talent',
        ];

        return fake()->randomElement($bios);
    }

    private function getRandomMission(): string
    {
        return "To build a life of purpose through consistent daily actions, focusing on health, meaningful work, and strong relationships.";
    }

    private function getRandomEulogy(): string
    {
        return "They lived with intention. Every day was an opportunity to grow, to help others, and to create something meaningful. They never stopped learning.";
    }

    private function getRandomBucketList(): array
    {
        $items = [
            ['text' => 'Travel to Japan', 'completed' => fake()->boolean(20)],
            ['text' => 'Write a book', 'completed' => fake()->boolean(10)],
            ['text' => 'Learn to fly a plane', 'completed' => false],
            ['text' => 'Run a marathon', 'completed' => fake()->boolean(30)],
            ['text' => 'Speak at a conference', 'completed' => fake()->boolean(15)],
            ['text' => 'Start a successful business', 'completed' => fake()->boolean(10)],
            ['text' => 'Learn a new language', 'completed' => fake()->boolean(25)],
        ];

        return fake()->randomElements($items, rand(4, 7));
    }

    private function getRandomDefinitionOfSuccess(): array
    {
        return [
            'career' => 'Running a profitable business that creates value',
            'health' => 'Maintaining fitness and mental clarity',
            'growth' => 'Learning something new every day',
            'relationships' => 'Being present and supportive',
            'financial' => 'Financial independence by age 45',
        ];
    }

    private function getRandomOdysseyPlan(): array
    {
        return [
            'current_path' => "Continue building skills while working full-time. Balance family and side projects.",
            'radical_path' => "Quit job, travel the world for a year while building online businesses.",
            'alternative_path' => "Focus on content creation and teaching others what I know.",
        ];
    }

    private function getRandomFutureCalendar(): array
    {
        return [
            'ideal_sunday' => "Wake up naturally. Family time. Exercise. Work on side project. Read. Reflect.",
            'ideal_tuesday' => "Morning workout. Deep work 8-12. Lunch walk. Collaboration 1-4. Family evening.",
        ];
    }
}
