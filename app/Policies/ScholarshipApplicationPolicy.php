<?php

namespace App\Policies;

use App\Models\ScholarshipApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScholarshipApplicationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // กำหนดให้ผู้ใช้ที่มีสิทธิ์ 'view applyscholar' สามารถดูรายชื่อทั้งหมดได้
        return $user->hasPermissionTo('applyscholars.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ScholarshipApplication $application): bool
    {
        // กำหนดให้ผู้ใช้ที่มีสิทธิ์ 'view applyscholarship' สามารถดูรายละเอียดของการสมัครนี้ได้
        return $user->hasPermissionTo('applyscholars.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // กำหนดให้ผู้ใช้ที่มีสิทธิ์ 'create applyscholarship' สามารถสร้างการสมัครได้
        return $user->hasPermissionTo('applyscholars.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ScholarshipApplication $application): bool
    {
        // กำหนดให้ผู้ใช้ที่มีสิทธิ์ 'update applyscholarship' สามารถอัปเดตการสมัครได้
        return $user->hasPermissionTo('applyscholars.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ScholarshipApplication $application): bool
    {
        // กำหนดให้ผู้ใช้ที่มีสิทธิ์ 'delete applyscholarship' สามารถลบการสมัครได้
        return $user->hasPermissionTo('applyscholars.delete');
    }

 
}
