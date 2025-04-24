<?php

namespace App\Http\Resources;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Payment
 */
class PaymentResource extends JsonResource
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
            'tariff_id' => $this->tariff_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->transaction_id,
            'status' => $this->status->value,
            'status_label' => $this->status->getLabel(),
            'liqpay_data' => $this->liqpay_data,
            'user' => new UserResource($this->whenLoaded('user')),
            'tariff' => new TariffResource($this->whenLoaded('tariff')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
