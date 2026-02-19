<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'class_id' => $this->class_id,
            'class_model' => $this->whenLoaded('classModel'),
            'teacher_id' => $this->teacher_id,
            'teacher' => $this->whenLoaded('teacher', function() {
                return new TeacherResource($this->teacher);
            }),
            'students_count' => $this->whenCounted('students', $this->students_count ?? $this->students->count()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
