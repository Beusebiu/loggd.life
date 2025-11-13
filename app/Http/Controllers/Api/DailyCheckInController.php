<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyCheckIn;
use App\Models\Goal;
use Illuminate\Http\Request;

class DailyCheckInController extends Controller
{
    public function index(Request $request)
    {
        $query = DailyCheckIn::where('user_id', $request->user()->id);

        // Support date range filtering
        if ($request->has('from')) {
            $query->where('date', '>=', $request->input('from'));
        }

        if ($request->has('to')) {
            $query->where('date', '<=', $request->input('to'));
        }

        $checkIns = $query->orderBy('date', 'desc')
            ->limit($request->input('limit', 30))
            ->get();

        return response()->json($checkIns);
    }

    public function show(Request $request, $date)
    {
        $checkIn = DailyCheckIn::where('user_id', $request->user()->id)
            ->where('date', $date)
            ->first();

        if (! $checkIn) {
            // Return empty structure for new check-in
            return response()->json([
                'date' => $date,
                'yesterday_priority_completed' => null,
                'yesterday_review' => null,
                'today_priority' => null,
                'today_tasks' => [],
                'goal_work_today' => false,
                'goal_work_details' => null,
                'day_reflection' => null,
                'mood' => null,
                'goals_worked_on' => [],
                'goal_specific_tasks' => [],
                'habit_completions' => [],
                'notes' => null,
                'is_public' => false,
                'share_wins_to_feed' => false,
            ]);
        }

        return response()->json($checkIn);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            // Morning Planning
            'yesterday_priority_completed' => 'nullable|boolean',
            'yesterday_review' => 'nullable|string',
            'today_priority' => 'nullable|string',
            'today_tasks' => 'nullable|array',
            'goal_work_today' => 'nullable|boolean',
            'goal_work_details' => 'nullable|string',
            // Evening Reflection
            'day_reflection' => 'nullable|string',
            'mood' => 'nullable|integer|min:1|max:10',
            // Other
            'goals_worked_on' => 'nullable|array',
            'goal_specific_tasks' => 'nullable|array',
            'habit_completions' => 'nullable|array',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
            'share_wins_to_feed' => 'boolean',
        ]);

        $validated['user_id'] = $request->user()->id;

        $checkIn = DailyCheckIn::updateOrCreate(
            ['user_id' => $request->user()->id, 'date' => $validated['date']],
            $validated
        );

        return response()->json($checkIn);
    }

    public function update(Request $request, $id)
    {
        $checkIn = DailyCheckIn::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            // Morning Planning
            'yesterday_priority_completed' => 'nullable|boolean',
            'yesterday_review' => 'nullable|string',
            'today_priority' => 'nullable|string',
            'today_tasks' => 'nullable|array',
            'goal_work_today' => 'nullable|boolean',
            'goal_work_details' => 'nullable|string',
            // Evening Reflection
            'day_reflection' => 'nullable|string',
            'mood' => 'nullable|integer|min:1|max:10',
            // Other
            'goals_worked_on' => 'nullable|array',
            'goal_specific_tasks' => 'nullable|array',
            'habit_completions' => 'nullable|array',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
            'share_wins_to_feed' => 'boolean',
        ]);

        $checkIn->update($validated);

        return response()->json($checkIn);
    }

    public function getActiveGoals(Request $request)
    {
        $goals = Goal::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->orderBy('order')
            ->get();

        return response()->json($goals);
    }
}
