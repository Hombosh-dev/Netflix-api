<?php

namespace App\Http\Resources;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Rating
 */
class RatingResource extends JsonResource
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
            'movie_id' => $this->movie_id,
            'number' => $this->number,
            'review' => $this->review,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'movie' => new MovieResource($this->whenLoaded('movie')),
        ];
    }
}
