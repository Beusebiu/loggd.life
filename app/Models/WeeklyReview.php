<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyReview extends Model
{
    protected $fillable = [
        'user_id',
        'week_start_date',
        'week_end_date',
        'total_check_ins',
        'completed_goals',
        'total_wins',
        'avg_energy',
        'avg_productivity',
        'avg_mood',
        'goal_progress',
        'biggest_win',
        'biggest_challenge',
        'what_i_learned',
        'vision_alignment',
        'next_week_goals',
        'next_week_focus',
        'notes',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'week_start_date' => 'date',
            'week_end_date' => 'date',
            'completed_goals' => 'array',
            'goal_progress' => 'array',
            'next_week_goals' => 'array',
            'is_public' => 'boolean',
            'avg_energy' => 'float',
            'avg_productivity' => 'float',
            'avg_mood' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateForWeek(\DateTime $weekStart, User $user): self
    {
        $weekEnd = (clone $weekStart)->modify('+6 days');

        // Get all daily check-ins for this week
        $dailyCheckIns = DailyCheckIn::where('user_id', $user->id)
            ->whereBetween('date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')])
            ->get();

        // Calculate stats
        $totalCheckIns = $dailyCheckIns->count();
        $totalWins = $dailyCheckIns->sum(fn ($checkIn) => count($checkIn->quick_wins ?? []));
        $avgEnergy = $dailyCheckIns->avg('energy_level');
        $avgProductivity = $dailyCheckIns->avg('productivity');
        $avgMood = $dailyCheckIns->avg('mood');

        return new self([
            'user_id' => $user->id,
            'week_start_date' => $weekStart->format('Y-m-d'),
            'week_end_date' => $weekEnd->format('Y-m-d'),
            'total_check_ins' => $totalCheckIns,
            'total_wins' => $totalWins,
            'avg_energy' => $avgEnergy ? round($avgEnergy, 1) : null,
            'avg_productivity' => $avgProductivity ? round($avgProductivity, 1) : null,
            'avg_mood' => $avgMood ? round($avgMood, 1) : null,
        ]);
    }
}
