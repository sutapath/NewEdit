<?php
// app/Policies/UserProfilePolicy.php
namespace App\Policies;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserProfilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any profiles.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('view-profile');
    }

    /**
     * Determine whether the user can view the profile.
     */
    public function view(User $user, UserProfile $userProfile)
    {
        return $user->hasPermissionTo('view-profile') && $user->id === $userProfile->user_id;
    }

    /**
     * Determine whether the user can create profiles.
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('update-profile');
    }

    /**
     * Determine whether the user can update the profile.
     */
    public function update(User $user, UserProfile $profile)
    {
        return $user->hasPermissionTo('update-profile') && $user->id === $profile->user_id;
    }
    
    /**
     * Determine whether the user can delete the profile.
     */
    public function delete(User $user, UserProfile $profile)
    {
        return $user->hasPermissionTo('update-profile') && $user->id === $profile->user_id;
    }
}
