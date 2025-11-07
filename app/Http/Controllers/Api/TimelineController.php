<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\CheckIn;
use App\Models\HabitCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TimelineController extends Controller
{
    /**
     * Get the user's timeline - a chronological feed of all activities.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $timeline = [];

        // Get date range (default to last 30 days)
        $from = $request->get('from', now()->subDays(30)->toDateString());
        $to = $request->get('to', now()->toDateString());

        // Fetch entries
        $entries = $user->entries()
            ->with('category')
            ->whereBetween('date', [$from, $to])
            ->get()
            ->map(function ($entry) {
                return [
                    'id' => $entry->id,
                    'type' => 'entry',
                    'date' => $entry->date,
                    'created_at' => $entry->created_at,
                    'content' => $entry->content,
                    'category' => $entry->category,
                    'is_public' => $entry->is_public,
                ];
            });

        // Fetch check-ins
        $checkIns = $user->checkIns()
            ->whereBetween('date', [$from, $to])
            ->get()
            ->map(function ($checkIn) {
                return [
                    'id' => $checkIn->id,
                    'type' => 'check-in',
                    'check_in_type' => $checkIn->type,
                    'date' => $checkIn->date,
                    'created_at' => $checkIn->created_at,
                    'content' => $checkIn->content,
                    'is_public' => $checkIn->is_public,
                ];
            });

        // Fetch habit checks grouped by date
        $habitChecks = $user->habitChecks()
            ->with('habit')
            ->whereBetween('date', [$from, $to])
            ->where('checked', true)
            ->get()
            ->groupBy('date')
            ->map(function ($checks, $date) {
                return [
                    'type' => 'habit-summary',
                    'date' => $date,
                    'created_at' => $checks->first()->created_at,
                    'count' => $checks->count(),
                    'habits' => $checks->map(function ($check) {
                        return [
                            'id' => $check->habit->id,
                            'name' => $check->habit->name,
                            'emoji' => $check->habit->emoji,
                            'note' => $check->note,
                        ];
                    })->values(),
                ];
            })
            ->values();

        // Merge all timeline items
        $timeline = collect([])
            ->merge($entries)
            ->merge($checkIns)
            ->merge($habitChecks)
            ->sortByDesc(function ($item) {
                return $item['date'] ?? $item['created_at'];
            })
            ->values();

        return response()->json([
            'data' => $timeline,
            'meta' => [
                'from' => $from,
                'to' => $to,
                'total' => $timeline->count(),
            ],
        ]);
    }

    /**
     * Get timeline statistics.
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        // Get date range (default to last 30 days)
        $from = $request->get('from', now()->subDays(30)->toDateString());
        $to = $request->get('to', now()->toDateString());

        $stats = [
            'entries_count' => $user->entries()
                ->whereBetween('date', [$from, $to])
                ->count(),
            'check_ins_count' => $user->checkIns()
                ->whereBetween('date', [$from, $to])
                ->count(),
            'habit_checks_count' => $user->habitChecks()
                ->whereBetween('date', [$from, $to])
                ->where('checked', true)
                ->count(),
            'active_days' => $this->getActiveDays($user, $from, $to),
        ];

        return response()->json($stats);
    }

    /**
     * Get count of active days (days with any activity).
     */
    private function getActiveDays($user, $from, $to)
    {
        $entryDates = $user->entries()
            ->whereBetween('date', [$from, $to])
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->toDateString());

        $checkInDates = $user->checkIns()
            ->whereBetween('date', [$from, $to])
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->toDateString());

        $habitCheckDates = $user->habitChecks()
            ->whereBetween('date', [$from, $to])
            ->where('checked', true)
            ->pluck('date')
            ->map(fn($date) => Carbon::parse($date)->toDateString());

        return collect([])
            ->merge($entryDates)
            ->merge($checkInDates)
            ->merge($habitCheckDates)
            ->unique()
            ->count();
    }
}
