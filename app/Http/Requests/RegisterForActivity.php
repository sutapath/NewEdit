<?php

namespace App\Http\Requests;

use App\Models\ActivityInformation;
use Illuminate\Foundation\Http\FormRequest;

class RegisterForActivityRequest extends FormRequest
{
    public function rules()
    {
        $activityId = $this->route('activity'); // Assuming the activity ID is passed as a route parameter
        $activity = ActivityInformation::findOrFail($activityId);

        return [
            'username' => 'required|exists:users,username',
            'activity_id' => 'required|exists:activity_informations,id',
            function ($attribute, $value, $fail) use ($activity) {
                if (!$activity->hasAvailableSlots()) {
                    $fail('ไม่สามารถลงทะเบียนได้เนื่องจากจำนวนที่ลงทะเบียนเต็ม');
                }
            },
        ];
    }
}
