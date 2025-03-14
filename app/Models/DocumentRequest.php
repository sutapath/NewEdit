<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequest extends Model
{
    use HasFactory; 
    protected $table = 'publish_requests'; 
    protected $fillable = [
        'user_id',
        'scholar_id',
        'document_type',
        'send_date',
        'type',
        'location',
    ]; 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
