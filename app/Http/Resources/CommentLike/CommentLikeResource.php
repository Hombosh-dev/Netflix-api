<?php

namespace App\Http\Resources\CommentLike;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentLikeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'comment_id' => $this->comment_id,
            'user_id' => $this->user_id,
            'is_liked' => $this->is_liked,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
