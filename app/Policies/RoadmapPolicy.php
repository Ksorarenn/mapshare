<?php

namespace App\Policies;

use App\Models\Roadmap;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoadmapPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the roadmap.
     * Public roadmaps are viewable by anyone; private (owned) roadmaps only by owner.
     */
    public function view(?User $user, Roadmap $roadmap): bool
    {
        // Assuming all roadmaps are public; if a 'public' flag existed, check it here.
        return true;
    }

    /**
     * Determine whether the user can update the roadmap.
     */
    public function update(User $user, Roadmap $roadmap): bool
    {
        return $user->id === $roadmap->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the roadmap.
     */
    public function delete(User $user, Roadmap $roadmap): bool
    {
        return $user->id === $roadmap->user_id || $user->isAdmin();
    }
}
