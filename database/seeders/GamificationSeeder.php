<?php

namespace Database\Seeders;

use App\ActivityType;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Services\GamificationService;
use App\UserLevel;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function __construct(
        protected GamificationService $gamificationService
    ) {}

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $targetUserCount = 500; // Can be changed to scale test
        $this->command->info("Seeding gamification data for up to {$targetUserCount} users...");

        // Clear existing activity data to avoid conflicts
        $this->command->info('Clearing existing activity data...');
        \DB::table('user_activity_logs')->truncate();
        \DB::table('users')->update([
            'total_points' => 0,
            'current_level' => 1,
            'current_streak' => 0,
            'longest_streak' => 0,
        ]);
        $this->command->info('✓ Cleared existing activity data');

        // Target levels for each tier - we'll ensure at least one user per tier
        $tierTargets = [
            1 => 2,      // Tier 1: Awakened (Level 1-2)
            2 => 4,      // Tier 2: Committed (Level 3-5)
            3 => 8,      // Tier 3: Devoted (Level 6-9)
            4 => 12,     // Tier 4: Relentless (Level 10-14)
            5 => 18,     // Tier 5: Unwavering (Level 15-20)
            6 => 24,     // Tier 6: Formidable (Level 21-27)
            7 => 32,     // Tier 7: Indomitable (Level 28-35)
            8 => 40,     // Tier 8: Invincible (Level 36-45)
            9 => 50,     // Tier 9: Immortal (Level 46-56)
            10 => 63,    // Tier 10: Eternal (Level 57-69)
            11 => 77,    // Tier 11: Omnipotent (Level 70-84)
            12 => 92,    // Tier 12: Absolute (Level 85-100)
        ];

        // Get all existing users
        $users = User::all();
        $totalUsers = $users->count();

        $this->command->info("Found {$totalUsers} existing users in database");

        // Ensure we have target number of users
        if ($totalUsers < $targetUserCount) {
            $usersToCreate = $targetUserCount - $totalUsers;
            $this->command->info("Creating {$usersToCreate} additional users...");

            // Find the highest existing user number to avoid conflicts
            $maxUserNumber = User::where('username', 'like', 'user%')
                ->get()
                ->map(fn ($u) => (int) str_replace('user', '', $u->username))
                ->max() ?? 0;

            // Batch create users for performance
            $usersData = [];
            for ($i = 0; $i < $usersToCreate; $i++) {
                $userNumber = $maxUserNumber + $i + 1;
                $usersData[] = [
                    'name' => fake()->name(),
                    'username' => "user{$userNumber}",
                    'email' => "user{$userNumber}@example.com",
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                    'profile_public' => rand(0, 1) === 1, // Random public/private
                    'timezone' => 'America/New_York',
                    'bio' => rand(0, 1) ? fake()->sentence() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Insert in batches of 100 for performance
                if (count($usersData) >= 100) {
                    User::insert($usersData);
                    $this->command->info('  Created '.count($usersData).' users...');
                    $usersData = [];
                }
            }

            // Insert remaining users
            if (! empty($usersData)) {
                User::insert($usersData);
                $this->command->info('  Created '.count($usersData).' users...');
            }

            // Refresh users list
            $users = User::all();
            $this->command->info("Total users now: {$users->count()}");
        }

        // Shuffle users for random distribution
        $users = $users->shuffle();

        // First, ensure we have 12 dedicated tier showcase users (all public for easy testing)
        $this->command->info('Creating dedicated tier showcase users...');
        $showcaseUsers = [];

        foreach ($tierTargets as $tier => $targetLevel) {
            // Find or create a showcase user for this tier
            $showcaseUsername = "tier{$tier}_showcase";
            $showcaseUser = User::where('username', $showcaseUsername)->first();

            if (! $showcaseUser) {
                $showcaseUser = User::create([
                    'name' => "Tier {$tier} Showcase",
                    'username' => $showcaseUsername,
                    'email' => "{$showcaseUsername}@example.com",
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                    'profile_public' => true, // Always public for testing
                    'timezone' => 'America/New_York',
                    'bio' => "Showcase user demonstrating Tier {$tier} achievements",
                ]);
                $this->command->info("  ✓ Created {$showcaseUsername}");
            } else {
                // Reset stats if user already exists
                $showcaseUser->update([
                    'total_points' => 0,
                    'current_level' => 1,
                    'current_streak' => 0,
                    'longest_streak' => 0,
                    'profile_public' => true, // Ensure public
                ]);
                $this->command->info("  ✓ Reset {$showcaseUsername}");
            }

            $showcaseUsers[] = $showcaseUser;
        }

        // Seed all showcase users with their respective tier data
        $assignedCount = 0;
        foreach ($tierTargets as $tier => $targetLevel) {
            $user = $showcaseUsers[$assignedCount];
            $daysActive = $this->calculateDaysForLevel($targetLevel);

            $this->command->info("Assigning {$user->username} to Tier {$tier} (Target Level {$targetLevel})...");
            $this->generateActivityForUser($user, $daysActive, $targetLevel);

            $assignedCount++;
        }

        // Now assign remaining users randomly (excluding showcase users)
        $this->command->info('Assigning remaining users to random tiers...');

        // Get showcase user IDs to exclude
        $showcaseUserIds = collect($showcaseUsers)->pluck('id')->toArray();

        // Assign remaining users to random tiers
        foreach ($users as $user) {
            // Skip showcase users (already assigned)
            if (in_array($user->id, $showcaseUserIds)) {
                continue;
            }

            // Random tier between 1-12
            $randomTier = rand(1, 12);
            $targetLevel = $tierTargets[$randomTier];
            $daysActive = $this->calculateDaysForLevel($targetLevel);

            $this->command->info("Assigning {$user->username} to random Tier {$randomTier} (Target Level {$targetLevel})...");
            $this->generateActivityForUser($user, $daysActive, $targetLevel);
        }

        $this->command->info('✅ Gamification seeding complete!');
        $this->command->info("All 12 tiers are represented in the database with {$users->count()} users total.");
    }

    /**
     * Calculate approximate days needed for a target level
     * Generate realistic historical data spanning multiple years
     */
    protected function calculateDaysForLevel(int $targetLevel): int
    {
        // Generate realistic long-term activity history
        // Lower levels: few months
        if ($targetLevel <= 5) {
            return rand(30, 90); // 1-3 months
        }

        // Mid levels: several months to a year
        if ($targetLevel <= 20) {
            return rand(90, 365); // 3-12 months
        }

        // Higher levels: 1-2 years of activity
        if ($targetLevel <= 50) {
            return rand(365, 730); // 1-2 years
        }

        // Top levels: 2+ years of consistent activity
        return rand(730, 1095); // 2-3 years
    }

    /**
     * Generate activity data for a user to reach approximately the target level
     * OPTIMIZED: Uses bulk inserts instead of individual logActivity calls
     */
    protected function generateActivityForUser(User $user, int $daysActive, int $targetLevel): void
    {
        // Calculate actual points needed based on leveling formula
        $pointsNeeded = UserLevel::pointsForLevel($targetLevel);

        // Calculate points per day needed
        $pointsPerDay = (int) ceil($pointsNeeded / $daysActive);

        // Distribute activities over the days
        $startDate = now()->subDays($daysActive);

        $activityLogs = [];
        $totalPoints = 0;

        for ($i = 0; $i < $daysActive; $i++) {
            $currentDate = Carbon::parse($startDate)->addDays($i);

            // Create realistic activity patterns with natural variation
            $isRecent = $i >= ($daysActive - 7);

            // Varied skip probability based on user's commitment level
            // More consistent users (higher levels) skip fewer days
            $skipProbability = max(5, 25 - ($targetLevel * 0.5)); // Higher levels = less skipping
            if (! $isRecent && rand(1, 100) < $skipProbability) {
                continue; // Skip this day
            }

            $dateString = $currentDate->toDateString();
            $isSameDay = $currentDate->isToday();

            // Realistic daily variation: some days very active, some days light
            // Create natural peaks and valleys in activity
            $activityMultiplier = 1.0;

            // Weekly patterns: weekends might be different
            if ($currentDate->isWeekend()) {
                $activityMultiplier = rand(0, 100) < 60 ? 1.3 : 0.7; // 60% more active, 40% less active
            }

            // Monthly motivation peaks (first week of month)
            if ($currentDate->day <= 7) {
                $activityMultiplier *= 1.2;
            }

            // Random good/bad days
            $randomFactor = rand(50, 150) / 100; // 0.5x to 1.5x

            $dailyTarget = (int) ($pointsPerDay * $activityMultiplier * $randomFactor);

            // Recent days get slight boost
            if ($isRecent) {
                $dailyTarget += rand(5, 15);
            }

            $dayPoints = 0;
            $activityCount = 0; // Track total activities for the day

            // Weekly review on Sundays (occasionally skipped)
            if ($currentDate->dayOfWeek === 0 && rand(1, 100) <= 70) { // 70% chance
                $points = $isSameDay ? 20 : 10;
                $activityLogs[] = [
                    'user_id' => $user->id,
                    'activity_date' => $dateString,
                    'activity_type' => ActivityType::WeeklyReview->value,
                    'related_id' => rand(1, 100),
                    'points_earned' => $points,
                    'is_same_day' => $isSameDay,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $dayPoints += $points;
                $activityCount++;
            }

            // Daily reflection (not every single day - 80% of days)
            if (rand(1, 100) <= 80) {
                $points = $isSameDay ? 10 : 5;
                $activityLogs[] = [
                    'user_id' => $user->id,
                    'activity_date' => $dateString,
                    'activity_type' => ActivityType::DailyReflection->value,
                    'related_id' => rand(1, 100),
                    'points_earned' => $points,
                    'is_same_day' => $isSameDay,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $dayPoints += $points;
                $activityCount++;
            }

            // Calculate how many habit checks needed to reach daily target
            $remainingPoints = max(0, $dailyTarget - $dayPoints);

            // Realistic habit check generation with max 18 activities per day total
            $maxActivitiesPerDay = 18; // Leave room for 0-18 range
            $remainingActivitySlots = max(0, $maxActivitiesPerDay - $activityCount);

            if ($remainingActivitySlots <= 0) {
                // Already at max activities, skip habit checks
                goto skipHabits;
            }

            $baseHabitPoints = $isSameDay ? 2 : 1;
            $targetHabitCount = (int) ($remainingPoints / $baseHabitPoints);

            // Cap habits to remaining activity slots
            $targetHabitCount = min($targetHabitCount, $remainingActivitySlots);

            // Realistic distribution: most days have 3-10 habits
            if ($targetHabitCount <= 10) {
                // Normal day: 3-10 habits
                $habitCheckCount = $targetHabitCount;
                $habitCheckPoints = $baseHabitPoints;
            } elseif ($targetHabitCount <= 15) {
                // Active day: cap at 15 habits
                $habitCheckCount = min(15, $targetHabitCount, $remainingActivitySlots);
                $habitCheckPoints = $baseHabitPoints;
            } else {
                // Very active day: cap at remaining slots, scale points
                $habitCheckCount = min($remainingActivitySlots, 16);
                $habitCheckPoints = $habitCheckCount > 0 ? (int) ceil($remainingPoints / $habitCheckCount) : $baseHabitPoints;
            }

            // Generate habit checks
            for ($j = 0; $j < $habitCheckCount; $j++) {
                $activityLogs[] = [
                    'user_id' => $user->id,
                    'activity_date' => $dateString,
                    'activity_type' => ActivityType::HabitCheck->value,
                    'related_id' => ($user->id * 10000) + ($i * 100) + $j,
                    'points_earned' => $habitCheckPoints,
                    'is_same_day' => $isSameDay,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $dayPoints += $habitCheckPoints;
                $activityCount++;
            }

            skipHabits:

            // Goal updates occasionally (5 points) - only if under max activities
            if ($activityCount < $maxActivitiesPerDay && rand(1, 100) <= 20) { // Reduced from 30% to 20%
                $points = $isSameDay ? 5 : 2;
                $activityLogs[] = [
                    'user_id' => $user->id,
                    'activity_date' => $dateString,
                    'activity_type' => ActivityType::GoalUpdate->value,
                    'related_id' => rand(1, 50),
                    'points_earned' => $points,
                    'is_same_day' => $isSameDay,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $dayPoints += $points;
                $activityCount++;
            }

            // Goal milestones rarely (15 points) - only if under max activities
            if ($activityCount < $maxActivitiesPerDay && rand(1, 100) <= 5) { // Reduced from 10% to 5%
                $points = $isSameDay ? 15 : 7;
                $activityLogs[] = [
                    'user_id' => $user->id,
                    'activity_date' => $dateString,
                    'activity_type' => ActivityType::GoalMilestone->value,
                    'related_id' => rand(1, 50),
                    'points_earned' => $points,
                    'is_same_day' => $isSameDay,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $dayPoints += $points;
                $activityCount++;
            }

            $totalPoints += $dayPoints;
        }

        // BULK INSERT - Much faster than individual inserts
        // Chunk to avoid MySQL placeholder limit (65535)
        if (! empty($activityLogs)) {
            $totalRecords = count($activityLogs);
            $chunks = array_chunk($activityLogs, 2000); // 2000 records per chunk for better performance
            $totalChunks = count($chunks);

            foreach ($chunks as $index => $chunk) {
                UserActivityLog::insert($chunk);
                // Progress feedback for large datasets
                if ($totalChunks > 5 && ($index + 1) % 5 === 0) {
                    $recordsInserted = ($index + 1) * 2000;
                    $this->command->info('    Progress: '.($index + 1)."/{$totalChunks} chunks (~".number_format(min($recordsInserted, $totalRecords)).' records)');
                }
            }
        }

        // Update user stats once at the end
        $this->gamificationService->updateUserStats($user);

        // Refresh user to get updated stats
        $user->refresh();
    }
}
