<?php

namespace App\Policies;

use App\Models\ActivitySave;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ActivitySavePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.anysave');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ActivitySave $activitySave): bool
    {
        return $user->hasPermissionTo('view.activitysave');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create.activitysave');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ActivitySave $activitySave): bool
    {
        return $user->hasPermissionTo('update.activitysave');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ActivitySave $activitySave): bool
    {
        return $user->hasPermissionTo('delete.activitysave');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ActivitySave $activitySave): bool
    {
        return $user->hasPermissionTo('restore.activitysave');
    }
 
}
