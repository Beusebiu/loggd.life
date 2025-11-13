<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vision extends Model
{
    protected $fillable = [
        'user_id',
        'eulogy_method',
        'bucket_list',
        'mission_prompt',
        'definition_of_success',
        'odyssey_plan',
        'future_calendar',
        'is_public',
        'private_sections',
    ];

    protected function casts(): array
    {
        return [
            'bucket_list' => 'array',
            'definition_of_success' => 'object',
            'odyssey_plan' => 'object',
            'future_calendar' => 'object',
            'private_sections' => 'array',
            'is_public' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function snapshots(): HasMany
    {
        return $this->hasMany(VisionSnapshot::class, 'user_id', 'user_id')
            ->orderBy('snapshot_date', 'desc');
    }

    public function isSectionPrivate(string $section): bool
    {
        return in_array($section, $this->private_sections ?? []);
    }

    public function toggleSectionPrivacy(string $section): void
    {
        $sections = $this->private_sections ?? [];

        if (in_array($section, $sections)) {
            $sections = array_values(array_filter($sections, fn ($s) => $s !== $section));
        } else {
            $sections[] = $section;
        }

        $this->private_sections = $sections;
        $this->save();
    }
}
