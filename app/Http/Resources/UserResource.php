<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'en_year' => $this->en_year, 
            'zip_code' => $this->zip_code, 
            'user' => new UserResource($this->whenLoaded('user')), // เพิ่มข้อมูลของผู้ใช้
            'roles' => $this->roles->map(function ($role) {
            return [
                'name' => $role->name,
            ];
        }),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
