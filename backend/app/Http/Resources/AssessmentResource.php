<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'subject_id' => $this->subject_id,
            'subject' => $this->whenLoaded('subject'),
            'class_id' => $this->class_id,
            'class_model' => $this->whenLoaded('classModel'),
            'term_id' => $this->term_id,
            'term' => $this->whenLoaded('term'),
            'total_marks' => $this->total_marks,
            'due_date' => $this->due_date,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'creator' => $this->whenLoaded('creator'),
            'submissions' => $this->whenLoaded('submissions'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
