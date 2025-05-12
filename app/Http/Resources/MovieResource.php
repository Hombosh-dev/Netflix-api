<?php

namespace App\Http\Resources;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Movie
 */
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
            'image_name' => $this->image_name,
            'poster' => $this->poster,
            'kind' => $this->kind->value,
            'status' => $this->status->value,
            'release_year' => $this->release_year,
            'imdb_score' => $this->imdb_score,
            'aliases' => $this->aliases ?? [],
            'countries' => $this->countries ?? [],
            'episodes_count' => $this->when($this->kind->value === 'tv_series' || $this->kind->value === 'animated_series',
                function () {
                    return $this->episodes_count;
                }
            ),
            'average_rating' => $this->averageRating,
            'main_genre' => $this->mainGenre,
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
