<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivitySave extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'user_id',
        'regis_id',
        'activity_id',
        'activity_name',
        'title',
        'fname',
        'lname',
        'date',
        'description',
        'file',
        'institution',
        'launch',
        'years',
        'end',
        'location',
        'hours',
        'result',
        'suggestions',
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

    public function activityRegister()
    {
        return $this->belongsTo(ActivityRegister::class, 'regis_id');
    }
    
}

