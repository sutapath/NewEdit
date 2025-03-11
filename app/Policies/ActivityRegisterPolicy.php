<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ActivityRegister;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityRegisterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any activity registers.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('activity_saves.view');
    }

    /**
     * Determine whether the user can view the activity register.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ActivityRegister  $activityRegister
     * @return bool
     */
    public function view(User $user, ActivityRegister $activityRegister)
    {
        return $user->hasPermissionTo('activity_saves.view') && $user->id === $activityRegister->user_id;
    }

    /**
     * Determine whether the user can create activity registers.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('activity_saves.create');
    }

    /**
     * Determine whether the user can update the activity register.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ActivityRegister  $activityRegister
     * @return bool
     */
    public function update(User $user, ActivityRegister $activityRegister)
    {
        return $user->hasPermissionTo('activity_saves.update') && $user->id === $activityRegister->user_id;
    }

    /**
     * Determine whether the user can delete the activity register.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ActivityRegister  $activityRegister
     * @return bool
     */
    public function delete(User $user, ActivityRegister $activityRegister)
    {
        return $user->hasPermissionTo('activity_saves.delete') && $user->id === $activityRegister->user_id;
    }

    /**
     * Determine whether the user can perform bulk updates.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function updatecheck(User $user)
    {
        return $user->hasPermissionTo('activity_saves.update_status');
    }
}
