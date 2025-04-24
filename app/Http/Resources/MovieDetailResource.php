<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieDetailResource extends JsonResource
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
            'backdrop' => $this->when(isset($this->backdrop), fn() => $this->backdrop, null),
            'poster' => $this->when(isset($this->poster), fn() => $this->poster, null),
            'image_name' => $this->when(isset($this->image_name), fn() => $this->image_name, null),
            'kind' => $this->kind->value,
            'status' => $this->status->value,
            'duration' => $this->duration,
            'formatted_duration' => $this->formattedDuration,
            'episodes_count' => $this->when($this->kind->value === 'tv_series' || $this->kind->value === 'animated_series',
                function () {
                    return $this->episodes_count;
                }
            ),
            'countries' => $this->countries,
            'aliases' => $this->aliases,
            'first_air_date' => $this->first_air_date,
            'last_air_date' => $this->when($this->kind->value === 'tv_series' || $this->kind->value === 'animated_series',
                function () {
                    return $this->last_air_date;
                }
            ),
            'year' => $this->releaseYear,
            'imdb_score' => $this->imdb_score,
            'is_published' => $this->is_published,
            'studio' => $this->whenLoaded('studio', function () {
                return [
                    'id' => $this->studio->id,
                    'name' => $this->studio->name,
                    'slug' => $this->studio->slug,
                    'description' => $this->studio->description,
                ];
            }),
            'tags' => $this->whenLoaded('tags', function () {
                return TagResource::collection($this->tags);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'seo' => [
                'title' => $this->meta_title,
                'description' => $this->meta_description,
                'image' => $this->meta_image,
            ],
        ];
    }
}
