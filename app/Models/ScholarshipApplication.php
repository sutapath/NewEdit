<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarshipApplication extends Model
{
    use HasFactory;
    protected $table = 'scholarship_applications';

    protected $fillable = [
        'scholar_id',
        'user_id',
        'title',
        'fname',
        'lname',
        'phone',
        'years',
        'faculty',
        'branch',
        'gpax',
        'imagefile',
        'scholar_form',
        'reg_form',
        'fee_receipt',
        'certificates',
        'tuition_fee',
        'scholar_type',
        'type_ability',
        'conduct_cert',
        'portfolio',
        'fam_cert',
        'award_certs',
        'leader_proof',
        'gpa_image',
        'id_card', 
        'result',
        'suggestions',
        'interview_date',
        'interview_score',
        'Interview_results',
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'scholar_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipient()
    {
        return $this->hasOne(ScholarshipRecipient::class, 'apply_id');
    }

    /**
     * Update related models based on Interview_results
     */
    public function updateBasedOnInterviewResults()
    {
        DB::transaction(function () {
            if ($this->Interview_results == 1) {
                Scholar::updateOrCreate(
                    ['user_id' => $this->user_id],
                    [ 'citizen_id' => $this->id_card,                     ]
                );
            } 
            
            if ($this->Interview_results == 0 ||$this->Interview_results == 2) {
 
               Scholar::where('citizen_id', $this->id_card)->delete();
            }
    
            // Create or update ScholarshipRecipient
            ScholarshipRecipient::updateOrCreate(
                ['apply_id' => $this->id],
                ['apply_id' => $this->id]
            );
        });
    }
    
}
