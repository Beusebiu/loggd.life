<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitCheck;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HabitController extends Controller
{
    public function __construct(protected AchievementService $achievementService) {}

    /**
     * Display a listing of the user's habits.
     * GET /api/habits
     */
    public function index(Request $request)
    {
        $query = $request->user()->habits();

        // Filter by status (active/archived)
        if ($request->has('status')) {
            if ($request->status === 'archived') {
                $query->archived();
            } else {
                $query->active();
            }
        } else {
            $query->active(); // Default to active habits
        }

        $habits = $query->with('checks')->orderBy('created_at', 'desc')->get();

        return response()->json($habits);
    }

    /**
     * Store a newly created habit.
     * POST /api/habits
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'emoji' => 'nullable|string|max:10',
            'frequency' => 'required|in:daily,weekdays,weekends,custom',
            'custom_days' => 'nullable|array',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'start_date' => 'required|date',
            'allow_multiple_checks' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $habit = $request->user()->habits()->create($validator->validated());

        return response()->json($habit, 201);
    }

    /**
     * Display the specified habit.
     * GET /api/habits/{id}
     */
    public function show($id)
    {
        $habit = auth()->user()->habits()->with('checks')->findOrFail($id);

        return response()->json($habit);
    }

    /**
     * Update the specified habit.
     * PUT /api/habits/{id}
     */
    public function update(Request $request, $id)
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'emoji' => 'nullable|string|max:10',
            'frequency' => 'sometimes|in:daily,weekdays,weekends,custom',
            'custom_days' => 'nullable|array',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'start_date' => 'sometimes|date',
            'allow_multiple_checks' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $habit->update($validator->validated());

        return response()->json($habit);
    }

    /**
     * Remove the specified habit.
     * DELETE /api/habits/{id}
     */
    public function destroy($id)
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        $habit->delete();

        return response()->json(['message' => 'Habit deleted successfully']);
    }

    /**
     * Archive a habit.
     * PATCH /api/habits/{id}/archive
     */
    public function archive($id)
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        $habit->update(['status' => 'archived']);

        return response()->json($habit);
    }

    /**
     * Reactivate an archived habit.
     * PATCH /api/habits/{id}/reactivate
     */
    public function reactivate($id)
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        $habit->update(['status' => 'active']);

        return response()->json($habit);
    }

    /**
     * Get checks for a habit within a date range.
     * GET /api/habits/{id}/checks
     */
    public function getChecks(Request $request, $id)
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        $query = $habit->checks();

        if ($request->has('from')) {
            $query->where('date', '>=', $request->from);
        }

        if ($request->has('to')) {
            $query->where('date', '<=', $request->to);
        }

        $checks = $query->orderBy('date', 'desc')->get();

        return response()->json($checks);
    }

    /**
     * Toggle a habit check for a specific date.
     * POST /api/habits/{id}/check
     */
    public function toggleCheck(Request $request, $id)
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'note' => 'nullable|string',
            'time' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // If habit allows multiple checks per day
        if ($habit->allow_multiple_checks) {
            // Add a new check with time
            $check = HabitCheck::create([
                'habit_id' => $habit->id,
                'user_id' => auth()->id(),
                'date' => $request->date,
                'time' => $request->time ?? now()->format('H:i:s'),
                'checked' => true,
                'note' => $request->note,
                'checked_at' => now(),
            ]);

            // Trigger achievement for multi-check habits too
            $this->triggerHabitAchievement($habit);

            return response()->json($check);
        }

        // Original behavior for single check per day
        $check = HabitCheck::where('habit_id', $habit->id)
            ->where('date', $request->date)
            ->first();

        if ($check) {
            // Toggle the check
            $wasChecked = $check->checked;
            $check->update([
                'checked' => ! $check->checked,
                'checked_at' => now(),
                'note' => $request->note ?? $check->note,
            ]);

            // Trigger achievement if newly checked
            if (! $wasChecked && $check->checked) {
                $this->triggerHabitAchievement($habit);
            }
        } else {
            // Create new check
            $check = HabitCheck::create([
                'habit_id' => $habit->id,
                'user_id' => auth()->id(),
                'date' => $request->date,
                'checked' => true,
                'note' => $request->note,
                'checked_at' => now(),
            ]);

            // Trigger achievement for new completion
            $this->triggerHabitAchievement($habit);
        }

        return response()->json($check);
    }

    /**
     * Add or update a note for a specific check.
     * PUT /api/habits/{id}/checks/{date}/note
     */
    public function updateCheckNote(Request $request, $id, $date)
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        $check = HabitCheck::where('habit_id', $habit->id)
            ->where('date', $date)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $check->update(['note' => $request->note]);

        return response()->json($check);
    }

    /**
     * Get habit statistics (streaks, completion rate).
     * GET /api/habits/{id}/stats
     */
    public function getStats($id)
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        // Get unique dates with at least one checked entry
        $uniqueDates = $habit->checks()
            ->where('checked', true)
            ->selectRaw('DISTINCT date')
            ->orderBy('date', 'asc')
            ->pluck('date')
            ->map(function ($date) {
                return $date instanceof \Carbon\Carbon ? $date->toDateString() : $date;
            })
            ->toArray();

        // Calculate current streak
        $currentStreak = 0;
        $today = now()->toDateString();
        $yesterday = date('Y-m-d', strtotime($today.' -1 day'));

        // Start from today if checked, otherwise start from yesterday (streak still active)
        $checkDate = in_array($today, $uniqueDates) ? $today : $yesterday;

        while (true) {
            if (! in_array($checkDate, $uniqueDates)) {
                break;
            }
            $currentStreak++;
            $checkDate = date('Y-m-d', strtotime($checkDate.' -1 day'));
        }

        // Calculate longest streak
        $longestStreak = 0;
        $tempStreak = 0;
        $prevDate = null;

        foreach ($uniqueDates as $dateStr) {
            $currentDate = \Carbon\Carbon::parse($dateStr);

            if ($prevDate === null) {
                $tempStreak = 1;
            } else {
                // Check if this date is exactly 1 day after the previous date
                if ($prevDate->diffInDays($currentDate) === 1) {
                    $tempStreak++;
                } else {
                    $longestStreak = max($longestStreak, $tempStreak);
                    $tempStreak = 1;
                }
            }

            $prevDate = $currentDate;
        }
        $longestStreak = max($longestStreak, $tempStreak);

        // Calculate total days since start
        $startDate = \Carbon\Carbon::parse($habit->start_date)->startOfDay();
        $todayDate = now()->startOfDay();
        $totalDays = $startDate->diffInDays($todayDate) + 1;

        // Ensure totalDays is positive
        if ($totalDays < 1) {
            $totalDays = 1;
        }

        $completedDays = count($uniqueDates);
        $completionRate = $totalDays > 0 ? round(($completedDays / $totalDays) * 100, 2) : 0;

        return response()->json([
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
            'total_completions' => $completedDays,
            'completion_rate' => $completionRate,
            'total_days' => $totalDays,
        ]);
    }

    /**
     * Get all habit checks for the authenticated user (for contribution graph).
     * GET /api/habit-checks?from=YYYY-MM-DD&to=YYYY-MM-DD
     */
    public function getAllChecks(Request $request)
    {
        $query = HabitCheck::whereHas('habit', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        });

        // Filter by date range
        if ($request->has('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->where('date', '<=', $request->to);
        }

        $checks = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->get();

        return response()->json($checks);
    }

    /**
     * Get all checks for a specific habit and date (for habits with multiple checks per day).
     * GET /api/habits/{id}/checks/{date}
     */
    public function getChecksForDate($habitId, $date)
    {
        $habit = auth()->user()->habits()->findOrFail($habitId);

        $checks = HabitCheck::where('habit_id', $habit->id)
            ->where('date', $date)
            ->orderBy('time', 'desc')
            ->get();

        return response()->json($checks);
    }

    /**
     * Delete a specific check (for habits with multiple checks per day).
     * DELETE /api/habit-checks/{checkId}
     */
    public function deleteCheck($checkId)
    {
        $check = HabitCheck::whereHas('habit', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($checkId);

        $check->delete();

        return response()->json(['message' => 'Check deleted successfully']);
    }

    /**
     * Trigger achievement for habit completion with current streak.
     */
    protected function triggerHabitAchievement(Habit $habit): void
    {
        // Calculate current streak
        $uniqueDates = $habit->checks()
            ->where('checked', true)
            ->selectRaw('DISTINCT date')
            ->orderBy('date', 'asc')
            ->pluck('date')
            ->map(function ($date) {
                return $date instanceof \Carbon\Carbon ? $date->toDateString() : $date;
            })
            ->toArray();

        $currentStreak = 0;
        $today = now()->toDateString();
        $yesterday = date('Y-m-d', strtotime($today.' -1 day'));
        $checkDate = in_array($today, $uniqueDates) ? $today : $yesterday;

        while (true) {
            if (! in_array($checkDate, $uniqueDates)) {
                break;
            }
            $currentStreak++;
            $checkDate = date('Y-m-d', strtotime($checkDate.' -1 day'));
        }

        // Trigger the achievement
        $this->achievementService->triggerHabitCompletion(
            auth()->user(),
            $habit,
            $currentStreak
        );
    }
}
