<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'officer_id',
        'activity_id',
        'regis_date',        
        'title',
        'fname',
        'lname',
        'act_check',
        'check_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(ActivityInformation::class, 'activity_id');
    }

    public function activitySaves()
    {
        return $this->hasMany(ActivitySave::class, 'regis_id');
    }
}
