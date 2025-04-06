<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Перетворює User у масив для JSON-відповіді.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'gender' => $this->gender,
            'avatar' => $this->avatar,
            'backdrop' => $this->backdrop,
            'description' => $this->description,
            'birthday' => $this->birthday,
            'allow_adult' => $this->allow_adult,
            'last_seen_at' => $this->last_seen_at,
            'is_auto_next' => $this->is_auto_next,
            'is_auto_play' => $this->is_auto_play,
            'is_auto_skip_intro' => $this->is_auto_skip_intro,
            'is_private_favorites' => $this->is_private_favorites,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
