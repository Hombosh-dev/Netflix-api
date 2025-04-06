<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'movie_id'       => $this->movie_id,
            'person_id'      => $this->person_id,
            'voice_person_id'=> $this->voice_person_id,
            'character_name' => $this->character_name,
        ];
    }
}
