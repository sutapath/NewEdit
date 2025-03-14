<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshipApplicationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'scholar_id' => $this->scholar_id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'phone' => $this->phone,
            'years' => $this->years,
            'faculty' => $this->faculty,
            'branch' => $this->branch,
            'gpax' => $this->gpax,
            'imagefile' => $this->imagefile,
            'scholar_form' => $this->scholar_form,
            'reg_form' => $this->reg_form,
            'fee_receipt' => $this->fee_receipt,
            'certificates' => $this->certificates,
            'tuition_fee' => $this->tuition_fee,
            'scholar_type' => $this->scholar_type,
            'type_ability' => $this->type_ability,
            'conduct_cert' => $this->conduct_cert,
            'portfolio' => $this->portfolio,
            'fam_cert' => $this->fam_cert,
            'award_certs' => $this->award_certs,
            'leader_proof' => $this->leader_proof,
            'gpa_image' => $this->gpa_image,
            'id_card' => $this->id_card, 
            'result' => $this->result,
            'suggestions' => $this->suggestions,
            'interview_date' => $this->interview_date,
            'interview_score' => $this->interview_score,
            'Interview_results' => $this->Interview_results,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
