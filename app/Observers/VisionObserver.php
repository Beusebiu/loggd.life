<?php

namespace App\Observers;

use App\ActivityType;
use App\Models\Vision;
use App\Services\GamificationService;

class VisionObserver
{
    public function __construct(
        private GamificationService $gamificationService
    ) {}

    public function created(Vision $vision): void
    {
        $this->gamificationService->logActivity(
            $vision->user,
            ActivityType::VisionUpdate,
            now(),
            $vision->id,
            Vision::class
        );
    }

    public function updated(Vision $vision): void
    {
        // Log activity on vision updates (meaningful content changes)
        // Skip if only privacy settings changed
        $significantFields = [
            'eulogy_method',
            'bucket_list',
            'mission_prompt',
            'definition_of_success',
            'odyssey_plan',
            'future_calendar',
        ];

        $hasSignificantChange = false;
        foreach ($significantFields as $field) {
            if ($vision->isDirty($field)) {
                $hasSignificantChange = true;
                break;
            }
        }

        if ($hasSignificantChange) {
            $this->gamificationService->logActivity(
                $vision->user,
                ActivityType::VisionUpdate,
                now(),
                $vision->id,
                Vision::class
            );
        }
    }
}
