<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Перетворює Comment у масив для JSON-відповіді.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'commentable_type' => $this->commentable_type,
            'commentable_id'   => $this->commentable_id,
            'user_id'          => $this->user_id,
            'is_spoiler'       => $this->is_spoiler,
            'body'             => $this->body,
            'parent_id'        => $this->parent_id,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
