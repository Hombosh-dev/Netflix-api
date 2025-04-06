<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudioResource extends JsonResource
{
    /**
     * Перетворює ресурс у масив для JSON-відповіді.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_image' => $this->meta_image,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'aliases' => $this->aliases,
            'is_genre' => $this->is_genre,
        ];
    }
}
