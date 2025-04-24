<?php

namespace App\Http\Resources;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Tag;
use App\Models\UserList;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin UserList
 */
class UserListResource extends JsonResource
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
            'user_id' => $this->user_id,
            'type' => $this->type->value,
            'listable_type' => $this->listable_type,
            'listable_id' => $this->listable_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'listable' => $this->when($this->relationLoaded('listable'), function () {
                return match ($this->listable_type) {
                    Movie::class => new MovieResource($this->listable),
                    Episode::class => new EpisodeResource($this->listable),
                    Person::class => new PersonResource($this->listable),
                    Tag::class => new TagResource($this->listable),
                    Selection::class => new SelectionResource($this->listable),
                    default => null,
                };
            }),
        ];
    }
}
