<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /** 
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'student_code' => ['nullable', 'string', 'max:20'],
            'title' => ['nullable', 'string', 'max:5'],
            'fname' => ['nullable', 'string', 'max:255'],
            'lname' => ['nullable', 'string', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:15'],
            'id_card' => ['nullable', 'string', 'max:15'],
            'faculty' => ['nullable', 'string', 'max:255'],
            'branch' => ['nullable', 'string', 'max:255'],
            'en_year' => ['nullable', 'string', 'max:5'],
            'house_no' => ['nullable', 'string', 'max:10'],
            'village_name' => ['nullable', 'string', 'max:255'],
            'sub_district' => ['nullable', 'string', 'max:255'],
            'district' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'zip_code' => ['nullable', 'string', 'max:15'], 
            'institution' => ['nullable', 'string', 'max:255'],
        ];
    }
}
