<?php

namespace App\Http\Controllers;

use App\Services\LeaderboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function __construct(protected LeaderboardService $leaderboardService) {}

    /**
     * Get all-time leaderboard
     */
    public function allTime(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 100);
        $leaderboard = $this->leaderboardService->getAllTimeLeaderboard($limit);

        return response()->json([
            'leaderboard' => $leaderboard,
            'type' => 'all-time',
        ]);
    }

    /**
     * Get weekly leaderboard
     */
    public function weekly(Request $request): JsonResponse
    {
        $limit = $request->integer('limit', 100);
        $leaderboard = $this->leaderboardService->getWeeklyLeaderboard($limit);

        return response()->json([
            'leaderboard' => $leaderboard,
            'type' => 'weekly',
            'week_start' => $this->leaderboardService->getWeekStart()->toDateString(),
        ]);
    }

    /**
     * Get current user's position in both leaderboards
     */
    public function myPosition(Request $request): JsonResponse
    {
        $user = $request->user();

        $allTimeRank = $this->leaderboardService->getUserAllTimeRank($user);
        $weeklyRank = $this->leaderboardService->getUserWeeklyRank($user);

        return response()->json([
            'all_time' => $allTimeRank,
            'weekly' => $weeklyRank,
        ]);
    }

    /**
     * Get near-miss notification if user is within 10 ranks of next position
     */
    public function nearMiss(Request $request): ?JsonResponse
    {
        $user = $request->user();
        $nearMiss = $this->leaderboardService->getNearMissNotification($user);

        if (! $nearMiss) {
            return response()->json(null);
        }

        return response()->json($nearMiss);
    }
}
