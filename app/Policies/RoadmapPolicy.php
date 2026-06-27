<?php

namespace App\Policies;

use App\Models\Roadmap;
use App\Models\User;

class RoadmapPolicy
{
    /**
     * Может ли пользователь редактировать/обновлять карту?
     */
    public function update(User $user, Roadmap $roadmap): bool
    {
        return $user->id === $roadmap->user_id;
    }

    /**
     * Может ли пользователь удалить карту?
     */
    public function delete(User $user, Roadmap $roadmap): bool
    {
        return $user->id === $roadmap->user_id || $user->role === 'admin';
    }
}