<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id'         => $this->id,
            'comment_id' => $this->comment_id,
            'user_id'    => $this->user_id,
            'type'       => $this->type,
            'is_viewed'  => $this->when(isset($this->is_viewed), $this->is_viewed),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
