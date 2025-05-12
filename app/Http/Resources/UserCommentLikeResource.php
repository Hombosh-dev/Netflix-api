<?php

namespace App\Http\Resources;

use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for comment likes in user context (without user relation)
 * 
 * @mixin CommentLike
 */
class UserCommentLikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'comment_id' => $this->comment_id,
            'is_liked' => $this->is_liked,
            'comment' => new CommentResource($this->whenLoaded('comment')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
