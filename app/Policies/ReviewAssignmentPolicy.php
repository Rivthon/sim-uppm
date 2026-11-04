<?php

namespace App\Policies;

use App\Models\ReviewAssignment;
use App\Models\User;

class ReviewAssignmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin_uppm') || $user->hasRole('reviewer');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReviewAssignment $reviewAssignment): bool
    {
        return $user->hasRole('admin_uppm') || $reviewAssignment->reviewer_id === $user->id;
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
    public function update(User $user, ReviewAssignment $reviewAssignment): bool
    {
        return $reviewAssignment->reviewer_id === $user->id
            && in_array($reviewAssignment->status, ['assigned', 'in_review'], true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReviewAssignment $reviewAssignment): bool
    {
        return $user->hasRole('admin_uppm') && $reviewAssignment->status === 'assigned';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReviewAssignment $reviewAssignment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReviewAssignment $reviewAssignment): bool
    {
        return false;
    }

    public function submit(User $user, ReviewAssignment $reviewAssignment): bool
    {
        return $this->update($user, $reviewAssignment);
    }
}
