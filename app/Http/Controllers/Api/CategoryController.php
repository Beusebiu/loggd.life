<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the user's categories.
     * GET /api/categories
     */
    public function index(Request $request)
    {
        $categories = $request->user()->categories()
            ->withCount('entries')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created category.
     * POST /api/categories
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = $request->user()->categories()->create([
            'name' => $request->name,
            'color' => $request->get('color', '#3B82F6'),
            'icon' => $request->icon,
        ]);

        return response()->json($category, 201);
    }

    /**
     * Display the specified category.
     * GET /api/categories/{id}
     */
    public function show(Request $request, $id)
    {
        $category = $request->user()->categories()
            ->withCount('entries')
            ->findOrFail($id);

        return response()->json($category);
    }

    /**
     * Update the specified category.
     * PUT/PATCH /api/categories/{id}
     */
    public function update(Request $request, $id)
    {
        $category = $request->user()->categories()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update($request->only(['name', 'color', 'icon']));

        return response()->json($category);
    }

    /**
     * Remove the specified category.
     * DELETE /api/categories/{id}
     */
    public function destroy(Request $request, $id)
    {
        $category = $request->user()->categories()->findOrFail($id);

        // Check if category has entries
        if ($category->entries()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing entries'
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
