<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLineNotify extends Model
{
    use HasFactory;

    protected $table = 'user_line_notify'; // ชื่อตาราง

    // กำหนดฟิลด์ที่สามารถทำการ insert/update ได้
    protected $fillable = [
        'user_id',
        'line_notify_token',
    ];

    // กำหนดความสัมพันธ์ระหว่าง UserLineNotify กับ User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
