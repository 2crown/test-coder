<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TermResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'academic_session_id' => $this->academic_session_id,
            'academic_session' => $this->whenLoaded('academicSession'),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_current' => $this->is_current,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
