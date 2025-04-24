<?php

namespace App\Http\Resources;

use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CommentLike
 */
class CommentLikeResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'comment' => new CommentResource($this->whenLoaded('comment')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
