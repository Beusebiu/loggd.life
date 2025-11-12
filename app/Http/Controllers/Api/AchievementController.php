<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AchievementService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function __construct(protected AchievementService $achievementService) {}

    /**
     * Get all pending (unshown) achievements for the authenticated user.
     */
    public function pending(Request $request)
    {
        $achievements = $this->achievementService->getPendingAchievements($request->user());

        return response()->json($achievements);
    }

    /**
     * Mark an achievement as shown.
     */
    public function markShown(Request $request, int $id)
    {
        $this->achievementService->markAsShown($id);

        return response()->json(['success' => true]);
    }

    /**
     * Get all achievements (for debugging).
     */
    public function all(Request $request)
    {
        $achievements = \App\Models\Achievement::forUser($request->user()->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json($achievements);
    }
}
