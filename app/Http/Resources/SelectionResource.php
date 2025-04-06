<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SelectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'slug'             => $this->slug,
            'name'             => $this->name,
            'description'      => $this->description,
            'meta_title'       => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_image'       => $this->meta_image,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
