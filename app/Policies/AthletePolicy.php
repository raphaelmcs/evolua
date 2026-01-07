<?php

namespace App\Policies;

use App\Models\Athlete;
use App\Models\User;

class AthletePolicy
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
    public function view(User $user, Athlete $athlete): bool
    {
        return $user->organization_id === $athlete->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->withinActiveLimit($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Athlete $athlete): bool
    {
        return $user->organization_id === $athlete->organization_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Athlete $athlete): bool
    {
        return $user->organization_id === $athlete->organization_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Athlete $athlete): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Athlete $athlete): bool
    {
        return false;
    }

    public function activate(User $user, Athlete $athlete): bool
    {
        return $user->organization_id === $athlete->organization_id
            && $this->withinActiveLimit($user);
    }

    protected function withinActiveLimit(User $user): bool
    {
        $organization = $user->organization;

        if (! $organization || ! $organization->subscription) {
            return true;
        }

        $limit = $organization->subscription->max_active_athletes;

        if (! $limit) {
            return true;
        }

        $activeCount = $organization->athletes()->where('active', true)->count();

        return $activeCount < $limit;
    }
}
