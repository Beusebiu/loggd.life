<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vision;
use App\Models\VisionSnapshot;
use Illuminate\Http\Request;

class VisionController extends Controller
{
    public function show(Request $request)
    {
        $vision = Vision::where('user_id', $request->user()->id)->first();

        if (! $vision) {
            // Return empty structure for first-time users
            return response()->json([
                'id' => null,
                'eulogy_method' => null,
                'bucket_list' => [],
                'mission_prompt' => null,
                'definition_of_success' => (object) [],
                'odyssey_plan' => (object) [],
                'future_calendar' => (object) [],
                'is_public' => true,
                'private_sections' => [],
            ]);
        }

        return response()->json($vision);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'eulogy_method' => 'nullable|string',
            'bucket_list' => 'nullable|array',
            'mission_prompt' => 'nullable|string',
            'definition_of_success' => 'nullable|array',
            'odyssey_plan' => 'nullable|array',
            'future_calendar' => 'nullable|array',
            'is_public' => 'boolean',
            'private_sections' => 'nullable|array',
        ]);

        $vision = Vision::updateOrCreate(
            ['user_id' => $request->user()->id],
            $validated
        );

        return response()->json($vision);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'eulogy_method' => 'nullable|string',
            'bucket_list' => 'nullable|array',
            'mission_prompt' => 'nullable|string',
            'definition_of_success' => 'nullable|array',
            'odyssey_plan' => 'nullable|array',
            'future_calendar' => 'nullable|array',
            'is_public' => 'boolean',
            'private_sections' => 'nullable|array',
        ]);

        $vision = Vision::where('user_id', $request->user()->id)->firstOrFail();
        $vision->update($validated);

        return response()->json($vision);
    }

    public function toggleSectionPrivacy(Request $request)
    {
        $validated = $request->validate([
            'section' => 'required|string|in:eulogy_method,bucket_list,mission_prompt,definition_of_success,odyssey_plan,future_calendar',
        ]);

        $vision = Vision::where('user_id', $request->user()->id)->firstOrFail();
        $vision->toggleSectionPrivacy($validated['section']);

        return response()->json($vision);
    }

    public function createSnapshot(Request $request)
    {
        $validated = $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        $vision = Vision::where('user_id', $request->user()->id)->firstOrFail();

        $snapshot = VisionSnapshot::create([
            'user_id' => $request->user()->id,
            'eulogy_method' => $vision->eulogy_method,
            'bucket_list' => $vision->bucket_list,
            'mission_prompt' => $vision->mission_prompt,
            'definition_of_success' => $vision->definition_of_success,
            'odyssey_plan' => $vision->odyssey_plan,
            'future_calendar' => $vision->future_calendar,
            'is_public' => $vision->is_public,
            'private_sections' => $vision->private_sections,
            'snapshot_date' => now(),
            'note' => $validated['note'] ?? null,
        ]);

        return response()->json($snapshot);
    }

    public function getSnapshots(Request $request)
    {
        $snapshots = VisionSnapshot::where('user_id', $request->user()->id)
            ->orderBy('snapshot_date', 'desc')
            ->get();

        return response()->json($snapshots);
    }

    public function getSnapshot(Request $request, int $id)
    {
        $snapshot = VisionSnapshot::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($snapshot);
    }
}
