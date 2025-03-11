<?php

namespace App\Policies;

use App\Models\ActivityInformation;
use App\Models\User;

class ActivityInformationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('activities.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivityInformation $activity): bool
    {
        return $user->hasPermissionTo('activity.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('activities.create')? true : false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActivityInformation $activity): bool
    {
        return $user->hasPermissionTo('activities.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivityInformation $activity): bool
    {
        return $user->hasPermissionTo('activities.delete');
    }
}
