<?php

namespace App\Policies;

use App\Models\FinalReport;
use App\Models\User;

class FinalReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin_uppm') || $user->hasRole('dosen');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FinalReport $finalReport): bool
    {
        return $user->hasRole('admin_uppm') || $finalReport->contract->proposal->lead_id === $user->id;
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
    public function update(User $user, FinalReport $finalReport): bool
    {
        return $finalReport->contract->proposal->lead_id === $user->id && $finalReport->status === 'revision';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FinalReport $finalReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FinalReport $finalReport): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FinalReport $finalReport): bool
    {
        return false;
    }

    public function verify(User $user, FinalReport $finalReport): bool
    {
        return $user->hasRole('admin_uppm');
    }
}
