<?php

namespace App\DTOs\Tariffs;

use App\DTOs\BaseDTO;
use Illuminate\Http\Request;

class TariffIndexDTO extends BaseDTO
{
    /**
     * Create a new TariffIndexDTO instance.
     *
     * @param string|null $query Search query
     * @param bool|null $isActive Filter by active status
     * @param string|null $currency Filter by currency
     * @param float|null $minPrice Filter by minimum price
     * @param float|null $maxPrice Filter by maximum price
     * @param int|null $durationDays Filter by duration days
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     */
    public function __construct(
        public readonly ?string $query = null,
        public readonly ?bool $isActive = null,
        public readonly ?string $currency = null,
        public readonly ?float $minPrice = null,
        public readonly ?float $maxPrice = null,
        public readonly ?int $durationDays = null,
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
            'q' => 'query',
            'is_active' => 'isActive',
            'currency',
            'min_price' => 'minPrice',
            'max_price' => 'maxPrice',
            'duration_days' => 'durationDays',
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
            query: $request->input('q'),
            isActive: $request->has('is_active') ? $request->boolean('is_active') : null,
            currency: $request->input('currency'),
            minPrice: $request->has('min_price') ? (float) $request->input('min_price') : null,
            maxPrice: $request->has('max_price') ? (float) $request->input('max_price') : null,
            durationDays: $request->has('duration_days') ? (int) $request->input('duration_days') : null,
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
        );
    }
}
