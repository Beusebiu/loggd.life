<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalUpdate extends Model
{
    protected $fillable = [
        'goal_id',
        'metric_value',
        'note',
        'milestones_snapshot',
        'update_date',
    ];

    protected function casts(): array
    {
        return [
            'milestones_snapshot' => 'array',
            'update_date' => 'datetime',
        ];
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
