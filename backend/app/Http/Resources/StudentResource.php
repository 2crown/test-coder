<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'admission_number' => $this->admission_number,
            'class_id' => $this->class_id,
            'class_model' => $this->whenLoaded('classModel', function() {
                return new ClassResource($this->classModel);
            }),
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'user' => $this->whenLoaded('user', function() {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'phone' => $this->user->phone,
                ];
            }),
            'subjects' => $this->whenLoaded('subjects', function() {
                return SubjectResource::collection($this->subjects);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
