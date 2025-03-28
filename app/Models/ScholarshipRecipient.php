<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'apply_id',
    ];

    public function application()
    {
        return $this->belongsTo(ScholarshipApplication::class, 'apply_id');
    }
}

