<?php

namespace App\Http\Requests\Journey;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date|before_or_equal:today',
            // Morning Planning
            'yesterday_priority_completed' => 'nullable|boolean',
            'yesterday_review' => 'nullable|string',
            'today_priority' => 'nullable|string',
            'today_tasks' => 'nullable|array',
            'today_tasks.*' => 'string|max:500',
            'goal_work_today' => 'nullable|boolean',
            'goal_work_details' => 'nullable|string',
            // Evening Reflection
            'day_reflection' => 'nullable|string',
            'mood' => 'nullable|integer|min:1|max:10',
            // Other
            'goals_worked_on' => 'nullable|array',
            'goals_worked_on.*' => 'integer|exists:goals,id',
            'goal_specific_tasks' => 'nullable|array',
            'goal_specific_tasks.*' => 'string|max:500',
            'habit_completions' => 'nullable|array',
            'habit_completions.*' => 'integer|exists:habits,id',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
            'share_wins_to_feed' => 'boolean',
        ];
    }
}
