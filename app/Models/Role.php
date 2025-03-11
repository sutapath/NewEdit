<?php

namespace App\Models; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // ถ้ามี user_id เป็น foreign key
        'name', 
        'guard_name'
    ];

    /**
     * ความสัมพันธ์ระหว่าง Scholar กับ User (ผู้สมัครทุน)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
}
