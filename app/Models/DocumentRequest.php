<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'publish_requests';

    // The attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'document_type',
        'send_date',
        'type',
    ];

    // Define a relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
