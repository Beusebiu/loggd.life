<?php

namespace App\Http\Requests\Journey;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeeklyReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'week_start_date' => 'required|date',
            'week_end_date' => 'required|date',
            'total_check_ins' => 'nullable|integer',
            'total_wins' => 'nullable|integer',
            'avg_energy' => 'nullable|numeric',
            'avg_productivity' => 'nullable|numeric',
            'avg_mood' => 'nullable|numeric',
            'goal_progress' => 'nullable|array',
            'goal_progress.*' => 'string|max:1000',
            'biggest_win' => 'nullable|string',
            'biggest_challenge' => 'nullable|string',
            'what_i_learned' => 'nullable|string',
            'vision_alignment' => 'nullable|integer|min:1|max:10',
            'next_week_goals' => 'nullable|array',
            'next_week_goals.*' => 'string|max:500',
            'next_week_focus' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_public' => 'boolean',
        ];
    }
}
