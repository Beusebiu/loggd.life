<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use App\Models\GoalMetric;
use App\Models\GoalMilestone;
use App\Models\GoalTask;
use App\Models\GoalUpdate;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    // GOAL CRUD
    public function index(Request $request)
    {
        $goals = Goal::where('user_id', $request->user()->id)
            ->with(['milestones', 'metrics', 'updates' => function ($query) {
                $query->latest('update_date')->limit(10);
            }, 'parent', 'children'])
            ->orderBy('order')
            ->get()
            ->map(function ($goal) {
                // Calculate progress based on tracking type
                if ($goal->tracking_type === 'milestone') {
                    $totalMilestones = $goal->milestones->count();
                    if ($totalMilestones > 0) {
                        $completedMilestones = $goal->milestones->where('completed', true)->count();
                        $goal->progress_percentage = (int) (($completedMilestones / $totalMilestones) * 100);
                    }
                }

                $goal->metric_progress_percentage = $goal->getMetricProgressPercentage();
                $goal->is_on_track = $goal->isOnTrack();
                $goal->trend_indicator = $goal->getTrendIndicator();

                return $goal;
            })
            ->groupBy('time_horizon');

        return response()->json($goals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'life_area' => 'required|in:career,health,relationships,financial,growth,impact,other',
            'time_horizon' => 'required|in:3_year,yearly,quarterly,monthly',
            'tracking_type' => 'required|in:metric,milestone,evolution,active',
            'metric_unit' => 'nullable|string|max:10',
            'metric_start_value' => 'nullable|numeric',
            'metric_target_value' => 'nullable|numeric',
            'metric_current_value' => 'nullable|numeric',
            'metric_decrease' => 'boolean',
            'period_identifier' => 'nullable|string',
            'parent_goal_id' => 'nullable|exists:goals,id',
            'target_date' => 'nullable|date',
            'is_public' => 'boolean',
            'vision_life_area' => 'nullable|string',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['started_at'] = now()->toDateString();

        $goal = Goal::create($validated);

        // Create initial update for metric goals
        if ($goal->tracking_type === 'metric' && $goal->metric_start_value) {
            GoalUpdate::create([
                'goal_id' => $goal->id,
                'metric_value' => $goal->metric_start_value,
                'note' => 'Initial value',
                'update_date' => now(),
            ]);
        }

        return response()->json($goal->load(['milestones', 'metrics', 'updates']));
    }

    public function show(Request $request, $id)
    {
        $goal = Goal::where('user_id', $request->user()->id)
            ->with(['milestones', 'metrics', 'updates' => function ($query) {
                $query->latest('update_date')->limit(10);
            }])
            ->findOrFail($id);

        // Calculate progress based on tracking type
        if ($goal->tracking_type === 'milestone') {
            $totalMilestones = $goal->milestones->count();
            if ($totalMilestones > 0) {
                $completedMilestones = $goal->milestones->where('completed', true)->count();
                $goal->progress_percentage = (int) (($completedMilestones / $totalMilestones) * 100);
            }
        }

        $goal->metric_progress_percentage = $goal->getMetricProgressPercentage();
        $goal->is_on_track = $goal->isOnTrack();
        $goal->trend_indicator = $goal->getTrendIndicator();

        return response()->json($goal);
    }

    public function update(Request $request, $id)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'life_area' => 'sometimes|in:career,health,relationships,financial,growth,impact,other',
            'time_horizon' => 'sometimes|in:3_year,yearly,quarterly,monthly',
            'status' => 'sometimes|in:active,completed,paused,abandoned',
            'progress_percentage' => 'sometimes|integer|min:0|max:100',
            'target_date' => 'nullable|date',
            'period_identifier' => 'nullable|string',
            'parent_goal_id' => 'nullable|exists:goals,id',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
            // Metric fields
            'metric_unit' => 'nullable|string|max:10',
            'metric_start_value' => 'nullable|numeric',
            'metric_target_value' => 'nullable|numeric',
            'metric_decrease' => 'boolean',
        ]);

        $goal->update($validated);

        // Recalculate progress after update
        if ($goal->tracking_type === 'milestone') {
            $goal->updateProgress();
        }

        // Reload with fresh data and calculated values
        $goal->load(['milestones', 'metrics', 'updates' => function ($query) {
            $query->latest('update_date')->limit(10);
        }]);

        // Calculate progress based on tracking type
        if ($goal->tracking_type === 'milestone') {
            $totalMilestones = $goal->milestones->count();
            if ($totalMilestones > 0) {
                $completedMilestones = $goal->milestones->where('completed', true)->count();
                $goal->progress_percentage = (int) (($completedMilestones / $totalMilestones) * 100);
            }
        }

        $goal->metric_progress_percentage = $goal->getMetricProgressPercentage();
        $goal->is_on_track = $goal->isOnTrack();
        $goal->trend_indicator = $goal->getTrendIndicator();

        return response()->json($goal);
    }

    public function destroy(Request $request, $id)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($id);
        $goal->delete();

        return response()->json(['message' => 'Goal deleted successfully']);
    }

    public function complete(Request $request, $id)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($id);
        $goal->markAsCompleted();

        return response()->json($goal);
    }

    // GOAL UPDATE (for metric/evolution tracking)
    public function addUpdate(Request $request, $id)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'metric_value' => 'nullable|numeric',
            'note' => 'nullable|string',
            'update_date' => 'nullable|date',
        ]);

        $validated['goal_id'] = $goal->id;
        $validated['update_date'] = $validated['update_date'] ?? now();

        // Update current value for metric goals
        if ($goal->tracking_type === 'metric' && isset($validated['metric_value'])) {
            $goal->metric_current_value = $validated['metric_value'];
            $goal->last_update_note = $validated['note'] ?? null;
            $goal->last_reviewed_at = now();
            $goal->save();
        }

        // Update last_update_note for evolution/journal goals
        if (($goal->tracking_type === 'evolution' || $goal->tracking_type === 'active') && isset($validated['note'])) {
            $goal->last_update_note = $validated['note'];
            $goal->last_reviewed_at = now();
            $goal->save();
        }

        $update = GoalUpdate::create($validated);

        return response()->json($goal->load(['updates']));
    }

    public function getUpdates(Request $request, $id)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($id);
        $updates = $goal->updates()->orderBy('update_date', 'desc')->paginate(20);

        return response()->json($updates);
    }

    // MILESTONE CRUD
    public function storeMilestone(Request $request, $goalId)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($goalId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_date' => 'nullable|date',
        ]);

        $validated['goal_id'] = $goal->id;
        $validated['order'] = $goal->milestones()->count();

        $milestone = GoalMilestone::create($validated);

        // Recalculate goal progress
        $goal->updateProgress();

        return response()->json($milestone);
    }

    public function updateMilestone(Request $request, $goalId, $milestoneId)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($goalId);
        $milestone = GoalMilestone::where('goal_id', $goalId)->findOrFail($milestoneId);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'completed' => 'sometimes|boolean',
            'target_date' => 'nullable|date',
        ]);

        if (isset($validated['completed']) && $validated['completed'] && ! $milestone->completed) {
            $milestone->markAsCompleted();
        } else {
            $milestone->update($validated);
        }

        // Recalculate goal progress
        $goal->updateProgress();

        return response()->json($milestone);
    }

    public function destroyMilestone(Request $request, $goalId, $milestoneId)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($goalId);
        $milestone = GoalMilestone::where('goal_id', $goalId)->findOrFail($milestoneId);
        $milestone->delete();

        // Recalculate goal progress
        $goal->updateProgress();

        return response()->json(['message' => 'Milestone deleted successfully']);
    }

    // TASK CRUD
    public function storeTask(Request $request, $goalId)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($goalId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'milestone_id' => 'nullable|exists:goal_milestones,id',
            'due_date' => 'nullable|date',
        ]);

        $validated['goal_id'] = $goal->id;
        $validated['order'] = $goal->tasks()->count();

        $task = GoalTask::create($validated);

        return response()->json($task);
    }

    public function updateTask(Request $request, $goalId, $taskId)
    {
        $task = GoalTask::where('goal_id', $goalId)->findOrFail($taskId);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'sometimes|boolean',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    public function toggleTask(Request $request, $goalId, $taskId)
    {
        $task = GoalTask::where('goal_id', $goalId)->findOrFail($taskId);
        $task->toggle();

        return response()->json($task);
    }

    public function destroyTask(Request $request, $goalId, $taskId)
    {
        $task = GoalTask::where('goal_id', $goalId)->findOrFail($taskId);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }

    // METRIC CRUD
    public function storeMetric(Request $request, $goalId)
    {
        $goal = Goal::where('user_id', $request->user()->id)->findOrFail($goalId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_value' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'direction' => 'required|in:increase,decrease',
        ]);

        $validated['goal_id'] = $goal->id;
        $validated['order'] = $goal->metrics()->count();

        $metric = GoalMetric::create($validated);

        return response()->json($metric);
    }

    public function updateMetric(Request $request, $goalId, $metricId)
    {
        $metric = GoalMetric::where('goal_id', $goalId)->findOrFail($metricId);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'target_value' => 'sometimes|numeric|min:0',
            'current_value' => 'sometimes|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'direction' => 'sometimes|in:increase,decrease',
        ]);

        $metric->update($validated);

        return response()->json($metric);
    }

    public function destroyMetric(Request $request, $goalId, $metricId)
    {
        $metric = GoalMetric::where('goal_id', $goalId)->findOrFail($metricId);
        $metric->delete();

        return response()->json(['message' => 'Metric deleted successfully']);
    }
}
