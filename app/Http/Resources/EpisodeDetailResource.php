<?php

namespace App\Http\Resources;

use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Episode
 */
class EpisodeDetailResource extends JsonResource
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
            'movie_id' => $this->movie_id,
            'movie' => new MovieResource($this->whenLoaded('movie')),
            'number' => $this->number,
            'name' => $this->name,
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'description' => $this->description,
            'duration' => $this->duration,
            'formatted_duration' => $this->formatted_duration,
            'air_date' => $this->air_date?->format('Y-m-d'),
            'is_filler' => $this->is_filler,
            'pictures_url' => $this->pictures_url,
            'video_players' => $this->video_players,
            'default_video_url' => asset('storage/videos/example.mp4'),
            'comments_count' => $this->comments_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_image' => $this->meta_image,
            ],
        ];
    }
}
