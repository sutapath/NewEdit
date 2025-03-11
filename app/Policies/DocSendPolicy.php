<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DocSend; 
use Illuminate\Auth\Access\HandlesAuthorization;

class DocSendPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any doc sends.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('docs_sends.view');
    }

    /**
     * Determine whether the user can view the doc send.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DocSend  $docSend
     * @return bool
     */
    public function view(User $user, DocSend $docSend)
    {
        // ตรวจสอบว่า user เป็นเจ้าของเอกสารหรือไม่
        if ($user->id === $docSend->user_id) {
            return true;
        }
    
        // ตรวจสอบว่า user มีบทบาทเป็น admin หรือ officer หรือไม่
        if ($user->hasRole('admin') || $user->hasRole('officer')) {
            return true;
        }
    
        // ถ้าไม่เข้าเงื่อนไขใด ๆ เลย ให้ return false
        return false;
    }
    
    /**
     * Determine whether the user can create doc sends.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('docs_sends.create');
    }

    /**
     * Determine whether the user can update the doc send.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DocSend  $docSend
     * @return bool
     */
    public function update(User $user, DocSend $docSend)
    {
        return $user->hasPermissionTo('docs_sends.update') && $user->id === $docSend->user_id;
    }

    /**
     * Determine whether the user can delete the doc send.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\DocSend  $docSend
     * @return bool
     */
    public function delete(User $user, DocSend $docSend): bool
    {
        return $user->hasPermissionTo('docs_sends.delete');
    }
 
}
