<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user_id' => $this->user_id,
            'is_spoiler' => $this->is_spoiler,
            'parent_id' => $this->parent_id,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
