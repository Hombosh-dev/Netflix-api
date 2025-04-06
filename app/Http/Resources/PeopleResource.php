<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeopleResource extends JsonResource
{
    /**
     * Перетворює People у масив для JSON-відповіді.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'slug'            => $this->slug,
            'name'            => $this->name,
            'original_name'   => $this->original_name,
            'image'           => $this->image,
            'description'     => $this->description,
            'birthday'        => $this->birthday,
            'birthplace'      => $this->birthplace,
            'meta_title'      => $this->meta_title,
            'meta_description'=> $this->meta_description,
            'meta_image'      => $this->meta_image,
            'type'            => $this->type,
            'gender'          => $this->gender,
            'full_name'       => $this->full_name,
            'age'             => $this->age,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
