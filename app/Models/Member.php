<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'members';

    // Allow mass assignment for these fields
    protected $fillable = [
        'citizen_id', 'title', 'fname', 'lname', 'code'
    ];

    public $timestamps = true; // Enable timestamps
}
