<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntryController extends Controller
{
    /**
     * Display a listing of the user's entries.
     * GET /api/entries
     */
    public function index(Request $request)
    {
        $query = $request->user()->entries()->with('category');

        // Filter by date range
        if ($request->has('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->where('date', '<=', $request->to);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sort
        $sort = $request->get('sort', 'desc');
        $query->orderBy('date', $sort);

        // Limit
        $limit = $request->get('limit', 100);
        $entries = $query->limit($limit)->get();

        return response()->json($entries);
    }

    /**
     * Store a newly created entry.
     * POST /api/entries
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'required|date',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $entry = $request->user()->entries()->create([
            'content' => $request->content,
            'category_id' => $request->category_id,
            'date' => $request->date,
            'is_public' => $request->get('is_public', false),
        ]);

        $entry->load('category');

        return response()->json($entry, 201);
    }

    /**
     * Display the specified entry.
     * GET /api/entries/{id}
     */
    public function show(Request $request, $id)
    {
        $entry = $request->user()->entries()->with('category')->findOrFail($id);
        return response()->json($entry);
    }

    /**
     * Update the specified entry.
     * PUT/PATCH /api/entries/{id}
     */
    public function update(Request $request, $id)
    {
        $entry = $request->user()->entries()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|required|string',
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'sometimes|required|date',
            'is_public' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $entry->update($request->only(['content', 'category_id', 'date', 'is_public']));
        $entry->load('category');

        return response()->json($entry);
    }

    /**
     * Remove the specified entry.
     * DELETE /api/entries/{id}
     */
    public function destroy(Request $request, $id)
    {
        $entry = $request->user()->entries()->findOrFail($id);
        $entry->delete();

        return response()->json(['message' => 'Entry deleted successfully']);
    }

    /**
     * Toggle the public visibility of an entry.
     * PATCH /api/entries/{id}/toggle-public
     */
    public function togglePublic(Request $request, $id)
    {
        $entry = $request->user()->entries()->findOrFail($id);
        $entry->is_public = !$entry->is_public;
        $entry->save();

        return response()->json($entry);
    }
}
