<?php

namespace App\Policies;

use App\Models\MonitoringEvaluation;
use App\Models\User;

class MonitoringEvaluationPolicy
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
    public function view(User $user, MonitoringEvaluation $monitoringEvaluation): bool
    {
        return $user->hasRole('admin_uppm') || $monitoringEvaluation->evaluator_id === $user->id;
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
    public function update(User $user, MonitoringEvaluation $monitoringEvaluation): bool
    {
        return $monitoringEvaluation->evaluator_id === $user->id
            && $monitoringEvaluation->status === 'scheduled';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MonitoringEvaluation $monitoringEvaluation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MonitoringEvaluation $monitoringEvaluation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MonitoringEvaluation $monitoringEvaluation): bool
    {
        return false;
    }
}
