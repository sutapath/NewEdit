<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    // หากต้องการให้ Laravel ทราบว่าตารางที่เกี่ยวข้องมีชื่อว่า 'majors'
    protected $table = 'majors';

    // ฟิลด์ที่อนุญาตให้กรอกข้อมูลได้ (หากต้องการระบุ)
    protected $fillable = [
        'faculty',
        'branch',
    ];
 
}
