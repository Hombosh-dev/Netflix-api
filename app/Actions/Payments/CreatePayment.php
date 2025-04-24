<?php

namespace App\Actions\Payments;

use App\DTOs\Payments\PaymentStoreDTO;
use App\Models\Payment;
use Lorisleiva\Actions\Concerns\AsAction;

class CreatePayment
{
    use AsAction;

    /**
     * Create a new payment.
     *
     * @param  PaymentStoreDTO  $dto
     * @return Payment
     */
    public function handle(PaymentStoreDTO $dto): Payment
    {
        // Create new payment
        $payment = new Payment();
        $payment->user_id = $dto->userId;
        $payment->tariff_id = $dto->tariffId;
        $payment->amount = $dto->amount;
        $payment->currency = $dto->currency;
        $payment->payment_method = $dto->paymentMethod;
        $payment->transaction_id = $dto->transactionId;
        $payment->status = $dto->status;
        $payment->liqpay_data = $dto->liqpayData;
        $payment->save();
        
        return $payment->load(['user', 'tariff']);
    }
}
