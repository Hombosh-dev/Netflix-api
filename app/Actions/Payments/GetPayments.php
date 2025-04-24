<?php

namespace App\Actions\Payments;

use App\DTOs\Payments\PaymentIndexDTO;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPayments
{
    use AsAction;

    /**
     * Get paginated list of payments with filtering, searching, and sorting.
     *
     * @param  PaymentIndexDTO  $dto
     * @return LengthAwarePaginator
     */
    public function handle(PaymentIndexDTO $dto): LengthAwarePaginator
    {
        // Start with base query
        $query = Payment::query()->with(['user', 'tariff']);

        // Apply filters
        if ($dto->userId) {
            $query->forUser($dto->userId);
        }

        if ($dto->tariffId) {
            $query->where('tariff_id', $dto->tariffId);
        }

        if ($dto->status !== null) {
            $query->withStatus($dto->status);
        }

        if ($dto->paymentMethod) {
            $query->where('payment_method', $dto->paymentMethod);
        }

        if ($dto->minAmount !== null) {
            $query->withAmountGreaterThan($dto->minAmount);
        }

        if ($dto->maxAmount !== null) {
            $query->where('amount', '<=', $dto->maxAmount);
        }

        if ($dto->currency) {
            $query->where('currency', $dto->currency);
        }

        if ($dto->dateFrom) {
            $query->where('created_at', '>=', Carbon::parse($dto->dateFrom));
        }

        if ($dto->dateTo) {
            $query->where('created_at', '<=', Carbon::parse($dto->dateTo));
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
