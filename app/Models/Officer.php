<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    use HasFactory;

protected $fillable = [
        'citizen_id', 'title', 'fname', 'lname', 'code' , 'type'
    ];

    /**
     * ความสัมพันธ์กับโมเดล User ถ้ามี
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id_card', 'citizen_id');
    }
}
