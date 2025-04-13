<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingsResource extends JsonResource
{
    /**
     * Перетворює Ratings у масив для JSON-відповіді.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'user_id'  => $this->user_id,
            'movie_id' => $this->movie_id,
            'number'   => $this->number,
            'review'   => $this->review,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
