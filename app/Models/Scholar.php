<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholar extends Model
{
    use HasFactory;

    protected $fillable = [
        'citizen_id', // ช่องที่สามารถกรอกข้อมูลได้
        'user_id', // ช่องที่สามารถกรอกข้อมูลได้
    ];

    /**
     * ความสัมพันธ์กับโมเดล User ถ้ามี
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id_card', 'citizen_id');
    }
}
