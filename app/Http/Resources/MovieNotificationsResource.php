<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieNotificationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id'   => $this->user_id,
            'movie_id'  => $this->movie_id,
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
