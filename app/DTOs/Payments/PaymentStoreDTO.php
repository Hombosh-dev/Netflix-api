<?php

namespace App\DTOs\Payments;

use App\DTOs\BaseDTO;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PaymentStoreDTO extends BaseDTO
{
    /**
     * Create a new PaymentStoreDTO instance.
     *
     * @param string $userId User ID
     * @param string $tariffId Tariff ID
     * @param float $amount Payment amount
     * @param string $currency Payment currency
     * @param string $paymentMethod Payment method
     * @param string $transactionId Transaction ID
     * @param PaymentStatus $status Payment status
     * @param array|Collection $liqpayData LiqPay data
     */
    public function __construct(
        public readonly string $userId,
        public readonly string $tariffId,
        public readonly float $amount,
        public readonly string $currency,
        public readonly string $paymentMethod,
        public readonly string $transactionId,
        public readonly PaymentStatus $status = PaymentStatus::PENDING,
        public readonly array|Collection $liqpayData = [],
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
            'amount',
            'currency',
            'payment_method' => 'paymentMethod',
            'transaction_id' => 'transactionId',
            'status',
            'liqpay_data' => 'liqpayData',
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
        $liqpayData = $request->input('liqpay_data', []);
        if (is_string($liqpayData)) {
            $liqpayData = json_decode($liqpayData, true) ?? [];
        }

        $status = PaymentStatus::PENDING;
        if ($request->has('status') && $request->input('status')) {
            try {
                $status = PaymentStatus::from($request->input('status'));
            } catch (\ValueError $e) {
                // Invalid status, use default
            }
        }

        return new static(
            userId: $request->input('user_id', $request->user()->id),
            tariffId: $request->input('tariff_id'),
            amount: (float) $request->input('amount'),
            currency: $request->input('currency', 'UAH'),
            paymentMethod: $request->input('payment_method', 'LiqPay'),
            transactionId: $request->input('transaction_id', (string) Str::uuid()),
            status: $status,
            liqpayData: $liqpayData,
        );
    }
}
