<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'student' => $this->whenLoaded('student'),
            'subject_id' => $this->subject_id,
            'subject' => $this->whenLoaded('subject'),
            'assessment_id' => $this->assessment_id,
            'assessment' => $this->whenLoaded('assessment'),
            'term_id' => $this->term_id,
            'term' => $this->whenLoaded('term'),
            'marks' => $this->marks,
            'grade' => $this->grade,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
