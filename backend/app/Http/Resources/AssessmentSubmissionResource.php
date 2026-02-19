<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'assessment_id' => $this->assessment_id,
            'assessment' => $this->whenLoaded('assessment'),
            'student_id' => $this->student_id,
            'student' => $this->whenLoaded('student', function() {
                return [
                    'id' => $this->student->id,
                    'admission_number' => $this->student->admission_number,
                    'user' => [
                        'name' => $this->student->user->name,
                        'email' => $this->student->user->email,
                    ]
                ];
            }),
            'file_path' => $this->file_path,
            'content' => $this->content,
            'submitted_at' => $this->submitted_at,
            'marks' => $this->marks,
            'feedback' => $this->feedback,
            'graded_by' => $this->graded_by,
            'grader' => $this->whenLoaded('grader'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
