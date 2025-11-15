<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vision;

class VisionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vision $vision): bool
    {
        return $user->id === $vision->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Vision $vision): bool
    {
        return $user->id === $vision->user_id;
    }

    public function delete(User $user, Vision $vision): bool
    {
        return $user->id === $vision->user_id;
    }

    public function restore(User $user, Vision $vision): bool
    {
        return $user->id === $vision->user_id;
    }

    public function forceDelete(User $user, Vision $vision): bool
    {
        return $user->id === $vision->user_id;
    }
}
