<?php

namespace App\Actions\Payments;

use App\DTOs\Payments\PaymentUpdateDTO;
use App\Models\Payment;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdatePayment
{
    use AsAction;

    /**
     * Update an existing payment.
     *
     * @param  Payment  $payment
     * @param  PaymentUpdateDTO  $dto
     * @return Payment
     */
    public function handle(Payment $payment, PaymentUpdateDTO $dto): Payment
    {
        // Update the payment
        if ($dto->tariffId !== null) {
            $payment->tariff_id = $dto->tariffId;
        }
        
        if ($dto->amount !== null) {
            $payment->amount = $dto->amount;
        }
        
        if ($dto->currency !== null) {
            $payment->currency = $dto->currency;
        }
        
        if ($dto->paymentMethod !== null) {
            $payment->payment_method = $dto->paymentMethod;
        }
        
        if ($dto->transactionId !== null) {
            $payment->transaction_id = $dto->transactionId;
        }
        
        if ($dto->status !== null) {
            $payment->status = $dto->status;
        }
        
        if ($dto->liqpayData !== null) {
            $payment->liqpay_data = $dto->liqpayData;
        }
        
        $payment->save();
        
        return $payment->load(['user', 'tariff']);
    }
}
