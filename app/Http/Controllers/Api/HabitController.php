<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HabitController extends Controller
{
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
            'date' => 'required|date|before_or_equal:today',
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

        // Calculate current streak (respecting frequency)
        $currentStreak = 0;
        $today = now();
        $checkDate = $today->copy();

        // Start from yesterday if today doesn't have a check yet
        if (! in_array($checkDate->toDateString(), $uniqueDates)) {
            $checkDate->subDay();
        }

        while (true) {
            $dateString = $checkDate->toDateString();
            $dayOfWeek = $checkDate->dayOfWeek;

            // Check if this date should be tracked based on habit frequency
            if ($this->shouldTrackDate($habit, $dayOfWeek)) {
                if (! in_array($dateString, $uniqueDates)) {
                    break; // Streak ends
                }
                $currentStreak++;
            }

            // Move to previous day
            $checkDate->subDay();

            // Safety: don't go back more than 1 year
            if ($checkDate->diffInDays($today) > 365) {
                break;
            }
        }

        // Calculate longest streak (respecting frequency)
        $longestStreak = 0;
        $tempStreak = 0;
        $prevDate = null;

        foreach ($uniqueDates as $dateStr) {
            $currentDate = \Carbon\Carbon::parse($dateStr);

            if ($prevDate === null) {
                $tempStreak = 1;
            } else {
                // Calculate expected days between checks based on frequency
                $daysBetween = $this->getExpectedDaysBetween($habit, $prevDate, $currentDate);

                if ($daysBetween === 0) {
                    // Dates are consecutive tracking days
                    $tempStreak++;
                } else {
                    // Streak broken
                    $longestStreak = max($longestStreak, $tempStreak);
                    $tempStreak = 1;
                }
            }

            $prevDate = $currentDate;
        }
        $longestStreak = max($longestStreak, $tempStreak);

        // Calculate total tracking days since start (respecting frequency)
        $startDate = \Carbon\Carbon::parse($habit->start_date)->startOfDay();
        $todayDate = now()->startOfDay();
        $totalTrackingDays = $this->countTrackingDays($habit, $startDate, $todayDate);

        // Ensure totalTrackingDays is positive
        if ($totalTrackingDays < 1) {
            $totalTrackingDays = 1;
        }

        $completedDays = count($uniqueDates);
        $completionRate = $totalTrackingDays > 0 ? round(($completedDays / $totalTrackingDays) * 100, 2) : 0;

        return response()->json([
            'current_streak' => $currentStreak,
            'longest_streak' => $longestStreak,
            'total_completions' => $completedDays,
            'completion_rate' => $completionRate,
            'total_days' => $totalTrackingDays,
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
        })->with('habit');

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
        })->with('habit')->findOrFail($checkId);

        $check->delete();

        return response()->json(['message' => 'Check deleted successfully']);
    }

    /**
     * Check if a date should be tracked based on habit frequency
     */
    private function shouldTrackDate($habit, int $dayOfWeek): bool
    {
        switch ($habit->frequency) {
            case 'daily':
                return true;

            case 'weekdays':
                // Monday = 1, Friday = 5
                return $dayOfWeek >= 1 && $dayOfWeek <= 5;

            case 'weekends':
                // Saturday = 6, Sunday = 0
                return $dayOfWeek === 0 || $dayOfWeek === 6;

            case 'custom':
                // Check if dayOfWeek is in custom_days array
                if (! $habit->custom_days) {
                    return true; // Default to true if custom_days not set
                }

                return in_array($dayOfWeek, $habit->custom_days);

            default:
                return true;
        }
    }

    /**
     * Check if two dates are consecutive tracking days (considering frequency)
     * Returns 0 if consecutive, >0 if there are skipped tracking days
     */
    private function getExpectedDaysBetween($habit, $prevDate, $currentDate): int
    {
        $checkDate = $prevDate->copy()->addDay();
        $skippedTrackingDays = 0;

        while ($checkDate->lt($currentDate)) {
            if ($this->shouldTrackDate($habit, $checkDate->dayOfWeek)) {
                $skippedTrackingDays++;
            }
            $checkDate->addDay();
        }

        return $skippedTrackingDays;
    }

    /**
     * Count total tracking days between two dates (respecting frequency)
     */
    private function countTrackingDays($habit, $startDate, $endDate): int
    {
        $checkDate = $startDate->copy();
        $trackingDays = 0;

        while ($checkDate->lte($endDate)) {
            if ($this->shouldTrackDate($habit, $checkDate->dayOfWeek)) {
                $trackingDays++;
            }
            $checkDate->addDay();
        }

        return $trackingDays;
    }
}
