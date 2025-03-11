<?php

namespace App\Policies;

use App\Models\Scholarship;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ScholarshipPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // กำหนดให้ผู้ใช้ที่มีสิทธิ์ 'view scholarships' สามารถดูรายชื่อทั้งหมดได้
        return $user->hasPermissionTo('scholarships.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Scholarship $scholarship): bool
    {
        // กำหนดให้ผู้ใช้ที่มีสิทธิ์ 'view scholarships' สามารถดูรายละเอียดของทุนการศึกษานี้ได้
        return $user->hasPermissionTo('scholarships.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('scholarships.create') ? true : false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Scholarship $scholarship): bool
    {
        return $user->hasPermissionTo('scholarships.update') ? true : false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Scholarship $scholarship): bool
    {
        // return $user->hasPermissionTo('scholarships.delete') ? true : false;
    }

 
}
