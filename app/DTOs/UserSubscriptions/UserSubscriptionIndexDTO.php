<?php

namespace App\DTOs\UserSubscriptions;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class UserSubscriptionIndexDTO extends BaseDTO
{
    /**
     * Create a new UserSubscriptionIndexDTO instance.
     *
     * @param string|null $userId Filter by user ID
     * @param string|null $tariffId Filter by tariff ID
     * @param bool|null $isActive Filter by active status
     * @param bool|null $autoRenew Filter by auto-renew status
     * @param string|null $startDateFrom Filter by start date from
     * @param string|null $startDateTo Filter by start date to
     * @param string|null $endDateFrom Filter by end date from
     * @param string|null $endDateTo Filter by end date to
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     */
    public function __construct(
        public readonly ?string $userId = null,
        public readonly ?string $tariffId = null,
        public readonly ?bool $isActive = null,
        public readonly ?bool $autoRenew = null,
        public readonly ?string $startDateFrom = null,
        public readonly ?string $startDateTo = null,
        public readonly ?string $endDateFrom = null,
        public readonly ?string $endDateTo = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $sort = 'created_at',
        public readonly string $direction = 'desc',
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
            'is_active' => 'isActive',
            'auto_renew' => 'autoRenew',
            'start_date_from' => 'startDateFrom',
            'start_date_to' => 'startDateTo',
            'end_date_from' => 'endDateFrom',
            'end_date_to' => 'endDateTo',
            'page',
            'per_page' => 'perPage',
            'sort',
            'direction',
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
            userId: $request->input('user_id'),
            tariffId: $request->input('tariff_id'),
            isActive: $request->has('is_active') ? $request->boolean('is_active') : null,
            autoRenew: $request->has('auto_renew') ? $request->boolean('auto_renew') : null,
            startDateFrom: $request->input('start_date_from'),
            startDateTo: $request->input('start_date_to'),
            endDateFrom: $request->input('end_date_from'),
            endDateTo: $request->input('end_date_to'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
        );
    }
}
