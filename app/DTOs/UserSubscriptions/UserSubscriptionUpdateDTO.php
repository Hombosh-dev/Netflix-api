<?php

namespace App\DTOs\UserSubscriptions;

use App\DTOs\BaseDTO;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserSubscriptionUpdateDTO extends BaseDTO
{
    /**
     * Create a new UserSubscriptionUpdateDTO instance.
     *
     * @param string|null $tariffId Tariff ID
     * @param Carbon|null $startDate Start date
     * @param Carbon|null $endDate End date
     * @param bool|null $isActive Whether the subscription is active
     * @param bool|null $autoRenew Whether the subscription auto-renews
     */
    public function __construct(
        public readonly ?string $tariffId = null,
        public readonly ?Carbon $startDate = null,
        public readonly ?Carbon $endDate = null,
        public readonly ?bool $isActive = null,
        public readonly ?bool $autoRenew = null,
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
            tariffId: $request->input('tariff_id'),
            startDate: $request->has('start_date') ? Carbon::parse($request->input('start_date')) : null,
            endDate: $request->has('end_date') ? Carbon::parse($request->input('end_date')) : null,
            isActive: $request->has('is_active') ? $request->boolean('is_active') : null,
            autoRenew: $request->has('auto_renew') ? $request->boolean('auto_renew') : null,
        );
    }
}
