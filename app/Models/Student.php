<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'citizen_id', 
        'title', 
        'fname', 
        'lname',  
        'student_code' 
    ];

    /**
     * ความสัมพันธ์กับโมเดล User
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id_card', 'citizen_id');
    }
}

