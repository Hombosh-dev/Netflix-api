<?php

namespace App\Http\Resources;

use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\MovieResource;
use App\Http\Resources\CommentResource;

/**
 * @mixin Episode
 */
class EpisodeResource extends JsonResource
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
            'number' => $this->number,
            'name' => $this->name,
            'slug' => $this->slug,
            'full_name' => $this->full_name,
            'description' => $this->description,
            'duration' => $this->duration,
            'formatted_duration' => $this->formatted_duration,
            'air_date' => $this->air_date?->format('Y-m-d'),
            'is_filler' => $this->is_filler,
            'pictures' => $this->pictures,
            'pictures_url' => $this->picturesUrl,
            'picture_url' => $this->picture_url,
            'video_players' => $this->video_players,
            'default_video_url' => asset('storage/videos/example.mp4'),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_image' => $this->meta_image,
            'meta_image_url' => $this->meta_image_url,
            'comments_count' => $this->comments_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'movie' => new MovieResource($this->whenLoaded('movie')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
