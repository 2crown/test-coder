<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author_id' => $this->author_id,
            'author' => $this->whenLoaded('author', function() {
                return [
                    'id' => $this->author->id,
                    'name' => $this->author->name,
                    'email' => $this->author->email,
                ];
            }),
            'class_id' => $this->class_id,
            'class_model' => $this->whenLoaded('classModel'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
