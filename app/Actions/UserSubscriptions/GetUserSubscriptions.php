<?php

namespace App\Actions\UserSubscriptions;

use App\DTOs\UserSubscriptions\UserSubscriptionIndexDTO;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetUserSubscriptions
{
    use AsAction;

    /**
     * Get paginated list of user subscriptions with filtering, searching, and sorting.
     *
     * @param  UserSubscriptionIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(UserSubscriptionIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = UserSubscription::query()->with(['user', 'tariff']);

        // Apply filters
        if ($dto->userId) {
            $query->forUser($dto->userId);
        }

        if ($dto->tariffId) {
            $query->forTariff($dto->tariffId);
        }

        if ($dto->isActive !== null) {
            if ($dto->isActive) {
                $query->active();
            } else {
                $query->inactive();
            }
        }

        if ($dto->autoRenew !== null) {
            if ($dto->autoRenew) {
                $query->autoRenewable();
            } else {
                $query->nonAutoRenewable();
            }
        }

        if ($dto->startDateFrom) {
            $query->where('start_date', '>=', Carbon::parse($dto->startDateFrom));
        }

        if ($dto->startDateTo) {
            $query->where('start_date', '<=', Carbon::parse($dto->startDateTo));
        }

        if ($dto->endDateFrom) {
            $query->where('end_date', '>=', Carbon::parse($dto->endDateFrom));
        }

        if ($dto->endDateTo) {
            $query->where('end_date', '<=', Carbon::parse($dto->endDateTo));
        }

        // Apply sorting
        $sortField = $dto->sort ?? 'created_at';
        $direction = $dto->direction ?? 'desc';
        $query->orderBy($sortField, $direction);

        // Return paginated results
        return $query->paginate(
            perPage: $dto->perPage,
            page: $dto->page
        );
    }
}
