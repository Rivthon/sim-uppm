<?php

namespace App\Policies;

use App\Models\FundingContract;
use App\Models\User;

class FundingContractPolicy
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
    public function view(User $user, FundingContract $fundingContract): bool
    {
        return $user->hasRole('admin_uppm') || $fundingContract->proposal->lead_id === $user->id;
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
    public function update(User $user, FundingContract $fundingContract): bool
    {
        return $user->hasRole('admin_uppm');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FundingContract $fundingContract): bool
    {
        return $user->hasRole('admin_uppm');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FundingContract $fundingContract): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FundingContract $fundingContract): bool
    {
        return false;
    }

    public function manageExecution(User $user, FundingContract $fundingContract): bool
    {
        return $fundingContract->proposal->lead_id === $user->id
            && in_array($fundingContract->status, ['signed', 'active'], true);
    }

    public function viewExecution(User $user, FundingContract $fundingContract): bool
    {
        return $this->view($user, $fundingContract)
            || $fundingContract->evaluations()->whereBelongsTo($user, 'evaluator')->exists();
    }
}
