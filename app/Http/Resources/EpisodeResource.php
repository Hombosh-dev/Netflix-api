<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    /**
     * Перетворює Episode у масив для JSON-відповіді.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'movie_id'       => $this->movie_id,
            'number'         => $this->number,
            'slug'           => $this->slug,
            'name'           => $this->name,
            'description'    => $this->description,
            'duration'       => $this->duration,
            'air_date'       => $this->air_date,
            'is_filler'      => $this->is_filler,
            'pictures'       => $this->pictures,
            'video_players'  => $this->video_players,
            'meta_title'     => $this->meta_title,
            'meta_description'=> $this->meta_description,
            'meta_image'     => $this->meta_image,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'picture_url'    => $this->picture_url,
            'pictures_url'   => $this->pictures_url,
        ];
    }
}
