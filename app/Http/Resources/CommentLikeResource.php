<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id'         => $this->id,
            'comment_id' => $this->comment_id,
            'user_id'    => $this->user_id,
            'is_liked'   => $this->is_liked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
