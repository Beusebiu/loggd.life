<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\Template;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    /**
     * Display a listing of the user's check-ins.
     */
    public function index(Request $request)
    {
        $query = $request->user()->checkIns()->orderBy('date', 'desc');

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->has('from')) {
            $query->where('date', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->where('date', '<=', $request->to);
        }

        $checkIns = $query->paginate(20);

        return response()->json($checkIns);
    }

    /**
     * Store a newly created check-in.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:daily,weekly,monthly,quarterly,yearly,custom',
            'date' => 'required|date',
            'content' => 'required|array',
            'is_public' => 'boolean',
        ]);

        $checkIn = $request->user()->checkIns()->create($validated);

        return response()->json($checkIn, 201);
    }

    /**
     * Display the specified check-in.
     */
    public function show(Request $request, $id)
    {
        $checkIn = $request->user()->checkIns()->findOrFail($id);

        return response()->json($checkIn);
    }

    /**
     * Update the specified check-in.
     */
    public function update(Request $request, $id)
    {
        $checkIn = $request->user()->checkIns()->findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|in:daily,weekly,monthly,quarterly,yearly,custom',
            'date' => 'sometimes|date',
            'content' => 'sometimes|array',
            'is_public' => 'sometimes|boolean',
        ]);

        $checkIn->update($validated);

        return response()->json($checkIn);
    }

    /**
     * Remove the specified check-in.
     */
    public function destroy(Request $request, $id)
    {
        $checkIn = $request->user()->checkIns()->findOrFail($id);
        $checkIn->delete();

        return response()->json(['message' => 'Check-in deleted successfully']);
    }

    /**
     * Get dashboard overview of all check-ins.
     * GET /api/check-ins/dashboard
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // Get current vision/goal documents
        $visionGoals = CheckIn::where('user_id', $user->id)
            ->whereHas('template', function ($query) {
                $query->whereIn('category', ['vision', 'goal']);
            })
            ->where('is_active', true)
            ->with('template')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get recent review entries (last 5)
        $recentReviews = CheckIn::where('user_id', $user->id)
            ->whereHas('template', function ($query) {
                $query->where('category', 'review');
            })
            ->with('template')
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        // Stats
        $stats = [
            'total_check_ins' => CheckIn::where('user_id', $user->id)->count(),
            'this_week' => CheckIn::where('user_id', $user->id)
                ->where('date', '>=', now()->startOfWeek())
                ->count(),
            'this_month' => CheckIn::where('user_id', $user->id)
                ->where('date', '>=', now()->startOfMonth())
                ->count(),
        ];

        return response()->json([
            'visionGoals' => $visionGoals,
            'recentReviews' => $recentReviews,
            'stats' => $stats,
        ]);
    }

    /**
     * Get all available templates (system + user templates).
     * GET /api/templates
     */
    public function getTemplates(Request $request)
    {
        $templates = Template::where(function ($query) use ($request) {
                $query->where('is_system', true)
                      ->orWhere('user_id', $request->user()->id);
            })
            ->where('is_hidden', false)
            ->orderBy('order')
            ->get()
            ->groupBy('category');

        return response()->json($templates);
    }

    /**
     * Get a specific template by ID.
     * GET /api/templates/{id}
     */
    public function getTemplate(Request $request, $id)
    {
        $template = Template::where(function ($query) use ($request) {
            $query->where('is_system', true)
                  ->orWhere('user_id', $request->user()->id);
        })->findOrFail($id);

        return response()->json($template);
    }

    /**
     * Get check-ins for a specific template.
     * GET /api/templates/{templateId}/check-ins
     */
    public function getCheckInsByTemplate(Request $request, $templateId)
    {
        $template = Template::findOrFail($templateId);

        $query = CheckIn::where('user_id', $request->user()->id)
            ->where('template_id', $templateId);

        // For singleton/versioned templates, only return active one
        if (in_array($template->behavior, ['singleton', 'versioned'])) {
            $checkIn = $query->where('is_active', true)->first();
            return response()->json($checkIn);
        }

        // For recurring templates, return all entries
        $checkIns = $query->orderBy('date', 'desc')->get();
        return response()->json($checkIns);
    }

    /**
     * Get version history for a template.
     * GET /api/templates/{templateId}/versions
     */
    public function getVersions(Request $request, $templateId)
    {
        $versions = CheckIn::where('user_id', $request->user()->id)
            ->where('template_id', $templateId)
            ->orderBy('version', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($versions);
    }

    /**
     * Store a new check-in using a template.
     * POST /api/templates/{templateId}/check-ins
     */
    public function storeWithTemplate(Request $request, $templateId)
    {
        $template = Template::findOrFail($templateId);

        $validated = $request->validate([
            'date' => 'required|date',
            'content' => 'required|array',
            'is_public' => 'boolean',
        ]);

        // Handle singleton/versioned behavior
        if (in_array($template->behavior, ['singleton', 'versioned'])) {
            // Deactivate previous version
            CheckIn::where('user_id', $request->user()->id)
                ->where('template_id', $templateId)
                ->update(['is_active' => false]);

            $latestVersion = CheckIn::where('user_id', $request->user()->id)
                ->where('template_id', $templateId)
                ->max('version') ?? 0;

            $validated['version'] = $latestVersion + 1;
            $validated['is_active'] = true;
        }

        $checkIn = $request->user()->checkIns()->create([
            'template_id' => $templateId,
            'type' => $template->period ?? 'custom',
            ...$validated,
        ]);

        $checkIn->load('template');

        return response()->json($checkIn, 201);
    }

    /**
     * Update an existing check-in.
     * PUT /api/check-ins/{id}
     */
    public function updateCheckIn(Request $request, $id)
    {
        $checkIn = $request->user()->checkIns()->findOrFail($id);

        $validated = $request->validate([
            'date' => 'sometimes|date',
            'content' => 'sometimes|array',
            'is_public' => 'sometimes|boolean',
        ]);

        $checkIn->update($validated);
        $checkIn->load('template');

        return response()->json($checkIn);
    }

    /**
     * Get check-in templates based on type (legacy support).
     */
    public function getTemplateLegacy(Request $request, $type)
    {
        $template = Template::where('period', $type)
            ->where('is_system', true)
            ->first();

        if (!$template) {
            return response()->json(['error' => 'Template not found'], 404);
        }

        return response()->json([
            'title' => $template->name,
            'questions' => $template->structure,
        ]);
    }
}
