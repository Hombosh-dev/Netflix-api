<?php

namespace App\DTOs\UserSubscriptions;

use App\DTOs\BaseDTO;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserSubscriptionStoreDTO extends BaseDTO
{
    /**
     * Create a new UserSubscriptionStoreDTO instance.
     *
     * @param string $userId User ID
     * @param string $tariffId Tariff ID
     * @param Carbon $startDate Start date
     * @param Carbon $endDate End date
     * @param bool $isActive Whether the subscription is active
     * @param bool $autoRenew Whether the subscription auto-renews
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $tariffId,
        public readonly Carbon $startDate,
        public readonly Carbon $endDate,
        public readonly bool $isActive = true,
        public readonly bool $autoRenew = false,
    ) {
    }

    /**
     * Get the fields that should be used for the DTO.
     *
     * @return array
     */
    public static function fields(): array
    {
        return [
            'user_id' => 'userId',
            'tariff_id' => 'tariffId',
            'start_date' => 'startDate',
            'end_date' => 'endDate',
            'is_active' => 'isActive',
            'auto_renew' => 'autoRenew',
        ];
    }

    /**
     * Create a new DTO instance from request.
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return new static(
            userId: $request->input('user_id', $request->user()->id),
            tariffId: $request->input('tariff_id'),
            startDate: Carbon::parse($request->input('start_date', now())),
            endDate: Carbon::parse($request->input('end_date')),
            isActive: $request->boolean('is_active', true),
            autoRenew: $request->boolean('auto_renew', false),
        );
    }
}
