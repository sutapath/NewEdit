<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateScholarshipApplicationsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'scholar_id' => 'string',
            'user_id' => 'exists:users,id',
            'title' => 'string',
            'fname' => 'string',
            'lname' => 'string',
            'phone' => 'string',
            'years' => 'string',
            'faculty' => 'string',
            'branch' => 'string',
            'gpax' => 'string',
            'imagefile' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'scholar_form' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'reg_form' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'fee_receipt' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'certificates' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'tuition_fee' => 'string',
            'scholar_type' => 'string',
            'Interview_results' => 'string',
            'type_ability' => 'nullable|string',
            'conduct_cert' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'portfolio' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'fam_cert' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'award_certs' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'leader_proof' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'gpa_image' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'id_card' => 'string',
            'email' => 'string|email',
            'result' => 'string',
            'suggestions' => 'nullable|string',
            'interview_date' => 'nullable|date',
            'interview_score' => 'nullable|numeric',
        ];
    }
}
