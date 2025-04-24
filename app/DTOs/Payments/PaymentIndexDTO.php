<?php

namespace App\DTOs\Payments;

use App\DTOs\BaseDTO;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;

class PaymentIndexDTO extends BaseDTO
{
    /**
     * Create a new PaymentIndexDTO instance.
     *
     * @param string|null $userId Filter by user ID
     * @param string|null $tariffId Filter by tariff ID
     * @param PaymentStatus|null $status Filter by payment status
     * @param string|null $paymentMethod Filter by payment method
     * @param float|null $minAmount Filter by minimum amount
     * @param float|null $maxAmount Filter by maximum amount
     * @param string|null $currency Filter by currency
     * @param string|null $dateFrom Filter by date from
     * @param string|null $dateTo Filter by date to
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @param string|null $sort Field to sort by
     * @param string $direction Sort direction (asc or desc)
     */
    public function __construct(
        public readonly ?string $userId = null,
        public readonly ?string $tariffId = null,
        public readonly ?PaymentStatus $status = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?float $minAmount = null,
        public readonly ?float $maxAmount = null,
        public readonly ?string $currency = null,
        public readonly ?string $dateFrom = null,
        public readonly ?string $dateTo = null,
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
            'status',
            'payment_method' => 'paymentMethod',
            'min_amount' => 'minAmount',
            'max_amount' => 'maxAmount',
            'currency',
            'date_from' => 'dateFrom',
            'date_to' => 'dateTo',
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
        $status = null;
        if ($request->has('status') && $request->input('status')) {
            try {
                $status = PaymentStatus::from($request->input('status'));
            } catch (\ValueError $e) {
                // Invalid status, ignore
            }
        }

        return new static(
            userId: $request->input('user_id'),
            tariffId: $request->input('tariff_id'),
            status: $status,
            paymentMethod: $request->input('payment_method'),
            minAmount: $request->has('min_amount') ? (float) $request->input('min_amount') : null,
            maxAmount: $request->has('max_amount') ? (float) $request->input('max_amount') : null,
            currency: $request->input('currency'),
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
            page: (int) $request->input('page', 1),
            perPage: (int) $request->input('per_page', 15),
            sort: $request->input('sort', 'created_at'),
            direction: $request->input('direction', 'desc'),
        );
    }
}
