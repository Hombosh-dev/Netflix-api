<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            // 'backdrop' => $this->backdrop, // Removed as it doesn't exist in the model
            'poster' => $this->poster,
            'kind' => $this->kind->value,
            'status' => $this->status->value,
            'year' => $this->releaseYear,
            'imdb_score' => $this->imdb_score,
            'studio' => $this->whenLoaded('studio', function () {
                return [
                    'id' => $this->studio->id,
                    'name' => $this->studio->name,
                    'slug' => $this->studio->slug,
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
