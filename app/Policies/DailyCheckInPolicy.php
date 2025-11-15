<?php

namespace App\Policies;

use App\Models\DailyCheckIn;
use App\Models\User;

class DailyCheckInPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $user->id === $dailyCheckIn->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $user->id === $dailyCheckIn->user_id;
    }

    public function delete(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $user->id === $dailyCheckIn->user_id;
    }

    public function restore(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $user->id === $dailyCheckIn->user_id;
    }

    public function forceDelete(User $user, DailyCheckIn $dailyCheckIn): bool
    {
        return $user->id === $dailyCheckIn->user_id;
    }
}
