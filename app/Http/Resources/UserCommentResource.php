<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Selection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource for comments in user context (without user relation)
 * 
 * @mixin Comment
 */
class UserCommentResource extends JsonResource
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
            'body' => $this->body,
            'is_spoiler' => $this->is_spoiler,
            'is_reply' => $this->is_reply,
            'parent_id' => $this->parent_id,
            'user_id' => $this->user_id,
            'commentable_type' => $this->commentable_type,
            'commentable_id' => $this->commentable_id,
            'commentable_type_label' => $this->getTranslatedTypeAttribute(),
            'likes_count' => $this->when(isset($this->likes_count), fn() => $this->likes_count ?? 0),
            'replies_count' => $this->when(isset($this->children_count), fn() => $this->children_count ?? 0),
            'parent' => new CommentResource($this->whenLoaded('parent')),
            'commentable' => $this->when($this->relationLoaded('commentable'), function () {
                return match ($this->commentable_type) {
                    Movie::class => new MovieResource($this->commentable),
                    Episode::class => new EpisodeResource($this->commentable),
                    Selection::class => new SelectionResource($this->commentable),
                    default => null,
                };
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
