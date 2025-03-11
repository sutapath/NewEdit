<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateScholarshipsRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust authorization as needed
    }

    public function rules()
{
    return [
        'scholar_name' => 'sometimes|string|max:255',
        'type' => 'sometimes|integer',
        'apply_date' => 'sometimes|date',
        'due_date' => 'sometimes|date',
        'status' => 'sometimes|integer',
        'description' => 'sometimes|string',
        'link' => 'string|url',
        'officer_code' => 'string',
        'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5012', // ตัวอย่างการตรวจสอบไฟล์
    ]; 
}
 
}