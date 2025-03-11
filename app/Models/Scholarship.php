<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'officer_code',
        'scholar_name',
        'apply_date',
        'due_date',
        'status',
        'description',
        'file',
        'type',
        'link',
        
    ];

    public function applications()
    {
        return $this->hasMany(ScholarshipApplication::class, 'scholar_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
    
}
