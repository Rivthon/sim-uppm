<?php

namespace App\Policies;

use App\Models\StrategicPlan;
use App\Models\User;

class StrategicPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin_uppm') || $user->hasRole('dosen') || $user->hasRole('reviewer') || $user->isStudent();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StrategicPlan $strategicPlan): bool
    {
        return $user->hasRole('admin_uppm') || $strategicPlan->status === 'active';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin_uppm');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StrategicPlan $strategicPlan): bool
    {
        return $user->hasRole('admin_uppm');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StrategicPlan $strategicPlan): bool
    {
        return $user->hasRole('admin_uppm');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StrategicPlan $strategicPlan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StrategicPlan $strategicPlan): bool
    {
        return false;
    }
}
