<?php

namespace App\Policies;

use App\Models\EvaluationTemplate;
use App\Models\User;

class EvaluationTemplatePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EvaluationTemplate $evaluationTemplate): bool
   {
        return ! $evaluationTemplate->organization_id
            || $user->organization_id === $evaluationTemplate->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EvaluationTemplate $evaluationTemplate): bool
    {
        return $evaluationTemplate->organization_id
            && $user->organization_id === $evaluationTemplate->organization_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EvaluationTemplate $evaluationTemplate): bool
    {
        return $evaluationTemplate->organization_id
            && $user->organization_id === $evaluationTemplate->organization_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EvaluationTemplate $evaluationTemplate): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EvaluationTemplate $evaluationTemplate): bool
    {
        return false;
    }
}
