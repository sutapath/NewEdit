<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityInformation extends Model
{
    use HasFactory;
    
    protected $table = 'activity_informations';
    
    protected $fillable = [
        'user_id',
        'activity_name',
        'start_date',
        // 'end_date',
        'location',
        'organizer',
        'description',
        'limits',
        'regis_due',
        'link',
        // 'hours',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function registrations()
    {
        return $this->hasMany(ActivityRegister::class, 'activity_id');
    }

    public function saves()
    {
        return $this->hasMany(ActivitySave::class, 'activity_id');
    }

    public function hasAvailableSlots()
    {
        $registrationsCount = $this->registrations()->count();
        return is_null($this->limits) || $registrationsCount < $this->limits;
    }
}
