<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the specified user's profile.
     */
    public function show(Request $request, string $username)
    {
        // Remove @ if it's included in the URL
        $username = ltrim($username, '@');

        // Find user by username
        $user = User::where('username', $username)->firstOrFail();

        // Check if the profile belongs to the authenticated user
        $isOwnProfile = $request->user() && $request->user()->id === $user->id;

        // Check if profile is public or if it's the user's own profile
        if (!$isOwnProfile && !$user->profile_public) {
            abort(403, 'This profile is private.');
        }

        // Get user stats
        $stats = $this->getUserStats($user, $isOwnProfile);

        return Inertia::render('Profile', [
            'profileUser' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'bio' => $user->bio,
                'profile_public' => $user->profile_public,
                'created_at' => $user->created_at->format('Y-m-d'),
            ],
            'stats' => $stats,
            'isOwnProfile' => $isOwnProfile,
        ]);
    }

    /**
     * Get user statistics
     */
    private function getUserStats(User $user, bool $isOwnProfile)
    {
        $stats = [
            'totalHabits' => $user->habits()->count(),
            'activeHabits' => $user->habits()->where('status', 'active')->count(),
            'totalEntries' => $user->entries()->count(),
            'totalChecks' => $user->habitChecks()->count(),
        ];

        // Calculate current streaks for active habits
        $activeHabits = $user->habits()->where('status', 'active')->get();
        $streaks = [];

        foreach ($activeHabits as $habit) {
            $checks = $user->habitChecks()
                ->where('habit_id', $habit->id)
                ->orderBy('date', 'desc')
                ->get();

            $currentStreak = 0;
            $today = now()->toDateString();
            $checkDate = $today;

            while (true) {
                $hasCheck = $checks->contains('date', $checkDate);
                if (!$hasCheck) {
                    break;
                }
                $currentStreak++;
                $checkDate = date('Y-m-d', strtotime($checkDate . ' -1 day'));
            }

            if ($currentStreak > 0) {
                $streaks[] = [
                    'habit_name' => $habit->name,
                    'habit_emoji' => $habit->emoji,
                    'streak' => $currentStreak,
                ];
            }
        }

        // Sort by streak length
        usort($streaks, fn($a, $b) => $b['streak'] <=> $a['streak']);

        $stats['streaks'] = $streaks;
        $stats['longestStreak'] = !empty($streaks) ? $streaks[0]['streak'] : 0;

        return $stats;
    }

    /**
     * Show the user's habits page
     */
    public function habits(Request $request, string $username)
    {
        $username = ltrim($username, '@');
        $user = User::where('username', $username)->firstOrFail();
        $isOwnProfile = $request->user() && $request->user()->id === $user->id;

        if (!$isOwnProfile) {
            abort(403, 'You can only view your own habits.');
        }

        return Inertia::render('Habits');
    }

    /**
     * Show the user's entries page
     */
    public function entries(Request $request, string $username)
    {
        $username = ltrim($username, '@');
        $user = User::where('username', $username)->firstOrFail();
        $isOwnProfile = $request->user() && $request->user()->id === $user->id;

        if (!$isOwnProfile && !$user->profile_public) {
            abort(403, 'This profile is private.');
        }

        return Inertia::render('Entries', [
            'isOwnProfile' => $isOwnProfile,
        ]);
    }
}
