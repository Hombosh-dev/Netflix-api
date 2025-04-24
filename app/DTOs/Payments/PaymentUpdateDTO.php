<?php

namespace App\DTOs\Payments;

use App\DTOs\BaseDTO;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PaymentUpdateDTO extends BaseDTO
{
    /**
     * Create a new PaymentUpdateDTO instance.
     *
     * @param string|null $tariffId Tariff ID
     * @param float|null $amount Payment amount
     * @param string|null $currency Payment currency
     * @param string|null $paymentMethod Payment method
     * @param string|null $transactionId Transaction ID
     * @param PaymentStatus|null $status Payment status
     * @param array|Collection|null $liqpayData LiqPay data
     */
    public function __construct(
        public readonly ?string $tariffId = null,
        public readonly ?float $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $transactionId = null,
        public readonly ?PaymentStatus $status = null,
        public readonly array|Collection|null $liqpayData = null,
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
        $liqpayData = null;
        if ($request->has('liqpay_data')) {
            $liqpayData = $request->input('liqpay_data', []);
            if (is_string($liqpayData)) {
                $liqpayData = json_decode($liqpayData, true) ?? [];
            }
        }

        $status = null;
        if ($request->has('status') && $request->input('status')) {
            try {
                $status = PaymentStatus::from($request->input('status'));
            } catch (\ValueError $e) {
                // Invalid status, ignore
            }
        }

        return new static(
            tariffId: $request->input('tariff_id'),
            amount: $request->has('amount') ? (float) $request->input('amount') : null,
            currency: $request->input('currency'),
            paymentMethod: $request->input('payment_method'),
            transactionId: $request->input('transaction_id'),
            status: $status,
            liqpayData: $liqpayData,
        );
    }
}
