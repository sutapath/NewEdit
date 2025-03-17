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
            'result' => 'string',
            'suggestions' => 'nullable|string',
            'interview_date' => 'nullable|date',
            'score_by' => 'nullable|string',
            'interview_score' => 'nullable|numeric',
            // ฟิลด์ใหม่สำหรับการยกเลิก  
            'cancellation_reason' => 'nullable|string',
            'contract_suggestions' => 'nullable|string',
            'cancel_status' => 'nullable|string|',
            'approved_by' => 'nullable|string|',
            'cancel_by' => 'nullable|string|',
            'cancel_date' => 'nullable|date',
            'approved_date' => 'nullable|date',
            'scholarship_contract' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'limits' => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        return [
            'gpax.string' => 'เกรดเฉลี่ยต้องเป็นตัวเลข',
            'imagefile.mimes' => 'ไฟล์ต้องเป็นประเภท pdf, doc, docx, jpg, jpeg หรือ png เท่านั้น',
            'imagefile.max' => 'ไฟล์ต้องมีขนาดไม่เกิน 2MB',
            'interview_score.numeric' => 'คะแนนสัมภาษณ์ต้องเป็นตัวเลข',
            'cancellation_reason.string' => 'เหตุผลในการยกเลิกต้องเป็นข้อความ',
            'cancel_status.string' => 'สถานะการยกเลิกต้องเป็นข้อความ',
            'approved_by.string' => 'ต้องระบุผู้อนุมัติเป็นข้อความ',
            'cancel_by.string' => 'ต้องระบุผู้ยกเลิกเป็นข้อความ',
            'cancel_by.scholarship_contract' => 'โปรดตรวจสอบรูปแบบของเอการ',
        ];
    }
}
