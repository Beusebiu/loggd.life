<?php

namespace App\Http\Requests\Habit;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'emoji' => 'nullable|string|max:10',
            'frequency' => 'sometimes|in:daily,weekdays,weekends,custom',
            'custom_days' => 'nullable|array',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'start_date' => 'sometimes|date',
            'allow_multiple_checks' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',
        ];
    }
}
