<?php

namespace App\Http\Requests\Goal;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'timeframe' => 'nullable|in:short_term,medium_term,long_term,lifetime',
            'target_date' => 'nullable|date',
            'parent_goal_id' => 'nullable|integer|exists:goals,id',
            'metric_type' => 'nullable|in:binary,numeric,percentage',
            'metric_target' => 'nullable|numeric',
            'metric_unit' => 'nullable|string|max:50',
            'is_public' => 'nullable|boolean',
        ];
    }
}
