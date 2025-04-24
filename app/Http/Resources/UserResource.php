<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role?->value,
            'gender' => $this->gender?->value,
            'avatar' => $this->avatar,
            'backdrop' => $this->backdrop,
            'description' => $this->description,
            'birthday' => $this->birthday?->format('Y-m-d'),
            'allow_adult' => $this->allow_adult,
            'is_auto_next' => $this->is_auto_next,
            'is_auto_play' => $this->is_auto_play,
            'is_auto_skip_intro' => $this->is_auto_skip_intro,
            'is_private_favorites' => $this->is_private_favorites,
            'is_banned' => $this->is_banned,
            'email_verified_at' => $this->email_verified_at,
            'last_seen_at' => $this->last_seen_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Add relationships if they are loaded
        if ($this->relationLoaded('userLists')) {
            $data['user_lists_count'] = $this->userLists->count();
            $data['user_lists'] = UserListResource::collection($this->userLists);
        }

        if ($this->relationLoaded('ratings')) {
            $data['ratings_count'] = $this->ratings->count();
            $data['ratings'] = RatingResource::collection($this->ratings);
        }

        if ($this->relationLoaded('comments')) {
            $data['comments_count'] = $this->comments->count();
            $data['comments'] = CommentResource::collection($this->comments);
        }

        if ($this->relationLoaded('subscriptions')) {
            $data['subscriptions_count'] = $this->subscriptions->count();
            $data['subscriptions'] = UserSubscriptionResource::collection($this->subscriptions);
        }

        // Add computed properties
        if ($this->birthday) {
            $data['age'] = $this->age;
        }

        if ($this->last_seen_at) {
            $data['is_online'] = $this->isOnline;
            $data['formatted_last_seen'] = $this->formattedLastSeen;
        }

        return $data;
    }
}
