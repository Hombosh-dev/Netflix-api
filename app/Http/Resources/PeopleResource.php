<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeopleResource extends JsonResource
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
            'biography' => $this->biography,
            'image' => $this->image,
            'type' => $this->type->value,
            'gender' => $this->gender?->value,
            'birth_date' => $this->birth_date,
            'death_date' => $this->death_date,
            'movies_count' => $this->when(isset($this->movies_count), $this->movies_count),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
