<?php

namespace App\Policies;

use App\Models\ResearchOutput;
use App\Models\User;

class ResearchOutputPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin_uppm') || $user->hasRole('dosen') || $user->isStudent();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ResearchOutput $researchOutput): bool
    {
        return $researchOutput->contract->proposal->isAccessibleBy($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('dosen');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ResearchOutput $researchOutput): bool
    {
        return $researchOutput->contract->proposal->lead_id === $user->id && $researchOutput->verification_status !== 'verified';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ResearchOutput $researchOutput): bool
    {
        return $user->hasRole('admin_uppm');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ResearchOutput $researchOutput): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ResearchOutput $researchOutput): bool
    {
        return false;
    }

    public function verify(User $user, ResearchOutput $researchOutput): bool
    {
        return $user->hasRole('admin_uppm');
    }
}
