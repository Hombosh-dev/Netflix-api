<?php

namespace App\Actions\Tariffs;

use App\DTOs\Tariffs\TariffIndexDTO;
use App\Models\Tariff;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetTariffs
{
    use AsAction;

    /**
     * Get paginated list of tariffs with filtering, searching, and sorting.
     *
     * @param  TariffIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(TariffIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Tariff::query()->withCount(['userSubscriptions']);

        // Apply search if query is provided
        if ($dto->query) {
            $query->where(function ($q) use ($dto) {
                $q->where('name', 'like', "%{$dto->query}%")
                  ->orWhere('description', 'like', "%{$dto->query}%");
            });
        }

        // Apply filters
        if ($dto->isActive !== null) {
            $query->where('is_active', $dto->isActive);
        }

        if ($dto->currency) {
            $query->where('currency', $dto->currency);
        }

        if ($dto->minPrice !== null) {
            $query->withPriceGreaterThan($dto->minPrice);
        }

        if ($dto->maxPrice !== null) {
            $query->withPriceLessThan($dto->maxPrice);
        }

        if ($dto->minPrice !== null && $dto->maxPrice !== null) {
            $query->withPriceBetween($dto->minPrice, $dto->maxPrice);
        }

        if ($dto->durationDays !== null) {
            $query->where('duration_days', $dto->durationDays);
        }

        // Apply sorting
        $sortField = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';

        if ($sortField === 'price') {
            $query->orderByPrice($direction);
        } elseif ($sortField === 'duration_days') {
            $query->orderBy('duration_days', $direction);
        } else {
            $query->orderBy($sortField, $direction);
        }

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
