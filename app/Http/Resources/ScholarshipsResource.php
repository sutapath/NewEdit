<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ScholarshipsResource extends JsonResource
{
        /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'officer_code' => $this->officer_code,
            'scholar_name' => $this->scholar_name,
            'apply_date' => $this->apply_date,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'description' => $this->description,
            'file' => $this->file,
            'link' => $this->link,
            'type' => $this->type, 
       ];
    }
}
