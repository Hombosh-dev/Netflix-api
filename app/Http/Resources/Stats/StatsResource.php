<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'users' => new UserStatsResource($this->resource['users']),
            'content' => new ContentStatsResource($this->resource['content']),
            'subscriptions' => new SubscriptionStatsResource($this->resource['subscriptions']),
            'payments' => new PaymentStatsResource($this->resource['payments']),
        ];
    }
}
