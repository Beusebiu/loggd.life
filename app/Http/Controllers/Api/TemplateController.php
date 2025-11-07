<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    /**
     * Get all templates (including visibility status).
     */
    public function index(Request $request)
    {
        $templates = Template::where('is_system', true)
            ->orWhere('user_id', $request->user()->id)
            ->orderBy('category')
            ->orderBy('order')
            ->get();

        return response()->json($templates);
    }

    /**
     * Create a new custom template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:vision,goal,review',
            'behavior' => 'required|in:singleton,versioned,recurring',
            'period' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'structure' => 'required|array',
            'structure.*.id' => 'required|string',
            'structure.*.label' => 'required|string',
            'structure.*.type' => 'required|in:textarea,text,select,number,checkbox,checklist',
            'structure.*.rows' => 'nullable|integer',
            'structure.*.placeholder' => 'nullable|string',
            'structure.*.options' => 'nullable|array',
            'structure.*.min' => 'nullable|integer',
            'structure.*.max' => 'nullable|integer',
        ]);

        // Get max order for user templates
        $maxOrder = Template::where('user_id', $request->user()->id)
            ->max('order') ?? 100;

        $template = Template::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'behavior' => $validated['behavior'],
            'period' => $validated['period'] ?? null,
            'icon' => $validated['icon'] ?? '📝',
            'structure' => $validated['structure'],
            'is_system' => false,
            'is_hidden' => false,
            'order' => $maxOrder + 1,
        ]);

        return response()->json($template, 201);
    }

    /**
     * Update a user template.
     */
    public function update(Request $request, $id)
    {
        $template = Template::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|in:vision,goal,review',
            'behavior' => 'sometimes|in:singleton,versioned,recurring',
            'period' => 'nullable|string',
            'icon' => 'nullable|string|max:10',
            'structure' => 'sometimes|array',
            'structure.*.id' => 'required|string',
            'structure.*.label' => 'required|string',
            'structure.*.type' => 'required|in:textarea,text,select,number,checkbox,checklist',
            'structure.*.rows' => 'nullable|integer',
            'structure.*.placeholder' => 'nullable|string',
            'structure.*.options' => 'nullable|array',
            'structure.*.min' => 'nullable|integer',
            'structure.*.max' => 'nullable|integer',
        ]);

        $template->update($validated);

        return response()->json($template);
    }

    /**
     * Delete a user template.
     */
    public function destroy(Request $request, $id)
    {
        $template = Template::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $template->delete();

        return response()->json(['message' => 'Template deleted successfully']);
    }

    /**
     * Toggle template visibility (for both system and user templates).
     */
    public function toggleVisibility(Request $request, $id)
    {
        $template = Template::where(function ($query) use ($request) {
            $query->where('is_system', true)
                  ->orWhere('user_id', $request->user()->id);
        })->findOrFail($id);

        // For system templates, we need to create a user preference
        // For now, we'll add is_hidden field to templates table
        $template->is_hidden = !($template->is_hidden ?? false);
        $template->save();

        return response()->json($template);
    }

    /**
     * Update template order.
     */
    public function reorder(Request $request, $id)
    {
        $template = Template::where(function ($query) use ($request) {
            $query->where('is_system', true)
                  ->orWhere('user_id', $request->user()->id);
        })->findOrFail($id);

        $validated = $request->validate([
            'order' => 'required|integer',
        ]);

        $template->order = $validated['order'];
        $template->save();

        return response()->json($template);
    }
}
