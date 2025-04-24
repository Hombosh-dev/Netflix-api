<?php

namespace App\Http\Resources;

use App\Models\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CommentReport
 */
class CommentReportResource extends JsonResource
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
            'type' => $this->type->value,
            'type_label' => $this->type->getLabel(),
            'is_viewed' => $this->is_viewed,
            'body' => $this->body,
            'user' => new UserResource($this->whenLoaded('user')),
            'comment' => new CommentResource($this->whenLoaded('comment')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
