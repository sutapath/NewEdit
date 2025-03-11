<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    // กำหนดชื่อ table
    protected $table = 'model_has_roles';

    // กำหนด primary key
    protected $primaryKey = ['role_id', 'model_id', 'model_type'];

    // กำหนดให้ primary key ใช้หลายคอลัมน์
    protected $keyType = 'array';

    // ไม่ให้ Laravel สร้าง timestamp fields อัตโนมัติ
    public $timestamps = false;

    // กำหนดคอลัมน์ที่เป็น primary key
    protected $fillable = ['role_id', 'model_id', 'model_type'];

    // การเชื่อมโยงกับ Role model
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // การเชื่อมโยงกับ Model (user, admin, หรืออะไรก็ตามที่มี roles)
    public function model()
    {
        return $this->morphTo();
    }
}
