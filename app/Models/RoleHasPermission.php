<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    // Specify the table associated with the model.
    protected $table = 'role_has_permissions';

    // Since this is a pivot table, you usually don't need timestamps.
    public $timestamps = false;

    // Define the fillable properties if needed.
    protected $fillable = [
        'permission_id', // or PermissionRegistrar::$pivotPermission
        'role_id',       // or PermissionRegistrar::$pivotRole
    ];

    // Define any relationships if needed (optional).
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
