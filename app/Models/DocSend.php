<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocSend extends Model
{
    use HasFactory;
    protected $table = 'docs_sends';

    protected $fillable = [
        'user_id',
        'title',
        'fname',
        'lname',
        'operat_docs', 
        'transcripts',
        'date',
        'years',
        'result',
        'suggestions', 
        'check_date',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
