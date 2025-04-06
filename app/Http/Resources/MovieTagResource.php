<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieTagResource extends JsonResource
{
    /**
     * Перетворює MovieTag у масив для JSON-відповіді.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'movie_id' => $this->movie_id,
            'tag_id'   => $this->tag_id,
        ];
    }
}
