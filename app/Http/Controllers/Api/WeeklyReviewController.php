<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeeklyReview;
use Illuminate\Http\Request;

class WeeklyReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = WeeklyReview::where('user_id', $request->user()->id)
            ->orderBy('week_start_date', 'desc')
            ->get();

        return response()->json($reviews);
    }

    public function show(Request $request, $weekStartDate)
    {
        $review = WeeklyReview::where('user_id', $request->user()->id)
            ->where('week_start_date', $weekStartDate)
            ->first();

        if (! $review) {
            // Generate new review with auto-populated stats
            $weekStart = new \DateTime($weekStartDate);
            $review = WeeklyReview::generateForWeek($weekStart, $request->user());
        }

        return response()->json($review);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date',
            'total_check_ins' => 'nullable|integer',
            'total_wins' => 'nullable|integer',
            'avg_energy' => 'nullable|numeric',
            'avg_productivity' => 'nullable|numeric',
            'avg_mood' => 'nullable|numeric',
            'goal_progress' => 'nullable|array',
            'goal_progress.*' => 'string|max:1000',
            'biggest_win' => 'nullable|string',
            'biggest_challenge' => 'nullable|string',
            'what_i_learned' => 'nullable|string',
            'vision_alignment' => 'nullable|integer|min:1|max:10',
            'next_week_goals' => 'nullable|array',
            'next_week_goals.*' => 'string|max:500',
            'next_week_focus' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $validated['user_id'] = $request->user()->id;

        $review = WeeklyReview::updateOrCreate(
            ['user_id' => $request->user()->id, 'week_start_date' => $validated['week_start_date']],
            $validated
        );

        return response()->json($review);
    }

    public function update(Request $request, $id)
    {
        $review = WeeklyReview::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'biggest_win' => 'nullable|string',
            'biggest_challenge' => 'nullable|string',
            'what_i_learned' => 'nullable|string',
            'vision_alignment' => 'nullable|integer|min:1|max:10',
            'next_week_goals' => 'nullable|array',
            'next_week_goals.*' => 'string|max:500',
            'next_week_focus' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $review->update($validated);

        return response()->json($review);
    }
}
