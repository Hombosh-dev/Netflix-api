<?php

namespace App\Http\Resources;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Person
 */
class PersonResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image,
            'birth_date' => isset($this->birth_date) ? $this->birth_date->format('Y-m-d') : null,
            'death_date' => isset($this->death_date) ? $this->death_date->format('Y-m-d') : null,
            'biography' => $this->biography ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
