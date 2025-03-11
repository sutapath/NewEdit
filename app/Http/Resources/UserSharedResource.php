<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSharedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'id_card' => $this->id_card,
            'phone' => $this->phone,
            'birthday' => $this->birthday,
            'title' => $this->title,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'student_code' => $this->student_code, 
            'branch' => $this->branch,
            'faculty' => $this->faculty,
            'house_no' => $this->house_no,         
            'village_name' => $this->village_name, 
            'sub_district' => $this->sub_district,  
            'district' => $this->district,         
            'province' => $this->province, 
            'zip_code' => $this->zip_code, 
            'en_year' => $this->en_year, 
        ];
    }
}
