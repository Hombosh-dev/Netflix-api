<?php

namespace App\Http\Resources\CommentReport;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentReportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'comment_id' => $this->comment_id,
            'user_id' => $this->user_id,
            'type' => $this->type,
            'description' => $this->description,
            'is_viewed' => $this->is_viewed,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
