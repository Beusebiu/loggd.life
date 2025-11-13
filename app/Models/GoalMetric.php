<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalMetric extends Model
{
    protected $fillable = [
        'goal_id',
        'name',
        'target_value',
        'current_value',
        'unit',
        'direction',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'target_value' => 'decimal:2',
            'current_value' => 'decimal:2',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function getProgressPercentageAttribute(): int
    {
        if ($this->target_value == 0) {
            return 0;
        }

        $percentage = ($this->current_value / $this->target_value) * 100;

        return min(100, max(0, (int) $percentage));
    }

    public function updateValue(float $newValue): void
    {
        $this->current_value = $newValue;
        $this->save();
    }
}
