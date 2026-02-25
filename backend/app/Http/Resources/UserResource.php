<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'avatar' => $this->avatar,
            'roles' => $this->roles->pluck('name'),
            'student' => $this->whenLoaded('student', function() {
                return new StudentResource($this->student);
            }),
            'teacher' => $this->whenLoaded('teacher', function() {
                return new TeacherResource($this->teacher);
            }),
            'parent' => $this->whenLoaded('parent', function() {
                return new ParentResource($this->parent);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
