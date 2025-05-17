<?php

namespace App\Http\Controllers;

use App\Actions\Payments\CreatePayment;
use App\DTOs\Payments\PaymentStoreDTO;
use App\Enums\PaymentStatus;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LiqPay;

class LiqPayController extends Controller
{
    /**
     * Create a LiqPay payment for a subscription
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createPayment(Request $request): JsonResponse
    {
        $request->validate([
            'tariff_id' => ['required', 'string', 'exists:tariffs,id'],
        ]);

        $tariff = Tariff::findOrFail($request->tariff_id);
        $user = auth()->user();

        // Generate a unique order ID
        $orderId = 'order_' . Str::uuid();

        // Create LiqPay instance
        $liqpay = new LiqPay(
            config('services.liqpay.public_key'),
            config('services.liqpay.private_key')
        );

        // Format amount with 2 decimal places
        $amount = number_format($tariff->price, 2, '.', '');

        // Prepare payment parameters
        $params = [
            'action' => 'pay',
            'amount' => $amount,
            'currency' => $tariff->currency ?? 'UAH',
            'description' => "Оплата підписки: {$tariff->name}",
            'order_id' => $orderId,
            'version' => '3',
            'result_url' => route('subscriptions.result'),
            'server_url' => route('liqpay.callback'),
            'public_key' => config('services.liqpay.public_key')
        ];

        // Store payment info in database for later use
        $payment = new Payment();
        $payment->user_id = $user->id;
        $payment->tariff_id = $tariff->id;
        $payment->amount = $amount;
        $payment->currency = $tariff->currency ?? 'UAH';
        $payment->payment_method = 'LiqPay';
        $payment->transaction_id = $orderId;
        $payment->status = PaymentStatus::PENDING;
        $payment->save();

        // Generate data and signature
        $data = base64_encode(json_encode($params));
        $signature = $liqpay->cnb_signature($params);

        // Construct full payment URL
        $fullCheckoutUrl = 'https://www.liqpay.ua/api/3/checkout?' . http_build_query([
            'data' => $data,
            'signature' => $signature,
        ]);

        // Return payment data for frontend to handle
        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'checkout_url' => 'https://www.liqpay.ua/api/3/checkout',
            'full_checkout_url' => $fullCheckoutUrl,
            'payment_params' => [
                'data' => $data,
                'signature' => $signature
            ]
        ]);
    }

    /**
     * Handle LiqPay callback
     *
     * @param Request $request
     * @param CreatePayment $action
     * @return JsonResponse
     */
    public function callback(Request $request, CreatePayment $action): JsonResponse
    {
        Log::info('LiqPay callback received', ['request' => $request->all()]);

        $liqpay = new LiqPay(
            config('services.liqpay.public_key'),
            config('services.liqpay.private_key')
        );

        $data = $request->input('data');
        $signature = $request->input('signature');

        // Verify signature
        $expectedSignature = $liqpay->str_to_sign(
            config('services.liqpay.private_key') . $data . config('services.liqpay.private_key')
        );

        if ($signature !== $expectedSignature) {
            Log::error('LiqPay callback: Invalid signature', ['data' => $data]);
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        // Decode payment data
        $paymentData = json_decode(base64_decode($data), true);
        Log::info('LiqPay payment data', ['paymentData' => $paymentData]);

        // Find existing payment or create a new one
        $payment = Payment::where('transaction_id', $paymentData['order_id'])->first();

        if (!$payment) {
            // Get user and tariff IDs from payment data
            $orderId = $paymentData['order_id'];
            $userId = null;
            $tariffId = null;
            $amount = $paymentData['amount'] ?? 0;
            $currency = $paymentData['currency'] ?? 'UAH';

            // Try to find payment info in database or use default values
            $existingPayment = Payment::where('transaction_id', $orderId)->first();
            if ($existingPayment) {
                $userId = $existingPayment->user_id;
                $tariffId = $existingPayment->tariff_id;
            }

            // Map LiqPay status to our PaymentStatus enum
            $status = $this->mapLiqPayStatus($paymentData['status']);

            // Create payment DTO
            $dto = new PaymentStoreDTO(
                userId: $userId,
                tariffId: $tariffId,
                amount: $amount,
                currency: $currency,
                paymentMethod: 'LiqPay',
                transactionId: $orderId,
                status: $status,
                liqpayData: $paymentData
            );

            // Create payment
            $payment = $action->handle($dto);
        } else {
            // Update existing payment status
            $status = $this->mapLiqPayStatus($paymentData['status']);
            $payment->status = $status;
            $payment->liqpay_data = $paymentData;
            $payment->save();
        }

        // If payment is successful, create or extend subscription
        if ($payment->isSuccessful()) {
            $this->processSuccessfulPayment($payment);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment result
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function result(Request $request): JsonResponse
    {
        // Check if we have payment data in the request
        $data = $request->input('data');
        $signature = $request->input('signature');

        if ($data && $signature) {
            $liqpay = new LiqPay(
                config('services.liqpay.public_key'),
                config('services.liqpay.private_key')
            );

            // Verify signature
            $expectedSignature = $liqpay->str_to_sign(
                config('services.liqpay.private_key') . $data . config('services.liqpay.private_key')
            );

            if ($signature === $expectedSignature) {
                $paymentData = json_decode(base64_decode($data), true);
                $payment = Payment::where('transaction_id', $paymentData['order_id'])->first();

                if ($payment) {
                    // Update payment status if needed
                    $status = $this->mapLiqPayStatus($paymentData['status']);
                    if ($payment->status !== $status) {
                        $payment->status = $status;
                        $payment->liqpay_data = $paymentData;
                        $payment->save();

                        // If payment became successful, process it
                        if ($payment->isSuccessful()) {
                            $this->processSuccessfulPayment($payment);
                        }
                    }

                    $subscription = null;

                    if ($payment->isSuccessful()) {
                        $subscription = UserSubscription::where('user_id', $payment->user_id)
                            ->where('is_active', true)
                            ->first();
                    }

                    return response()->json([
                        'success' => true,
                        'payment' => new PaymentResource($payment),
                        'subscription' => $subscription ? [
                            'id' => $subscription->id,
                            'start_date' => $subscription->start_date,
                            'end_date' => $subscription->end_date,
                            'is_active' => $subscription->is_active,
                        ] : null,
                        'status' => $payment->status->value,
                        'message' => $payment->isSuccessful()
                            ? 'Оплата успішна! Ваша підписка активована.'
                            : 'Статус оплати: ' . $payment->status->getLabel()
                    ]);
                }
            }
        }

        // Return error response if no valid payment data
        return response()->json([
            'success' => false,
            'message' => 'Не вдалося знайти інформацію про оплату.'
        ]);
    }

    /**
     * Map LiqPay status to our PaymentStatus enum
     *
     * @param string $liqpayStatus
     * @return PaymentStatus
     */
    private function mapLiqPayStatus(string $liqpayStatus): PaymentStatus
    {
        return match ($liqpayStatus) {
            'success' => PaymentStatus::SUCCESS,
            'wait_accept' => PaymentStatus::PENDING,
            'failure', 'error' => PaymentStatus::FAILED,
            'reversed', 'refund' => PaymentStatus::REFUNDED,
            default => PaymentStatus::PENDING,
        };
    }

    /**
     * Process successful payment by creating or extending subscription
     *
     * @param Payment $payment
     * @return void
     */
    private function processSuccessfulPayment(Payment $payment): void
    {
        $tariff = Tariff::findOrFail($payment->tariff_id);
        $userId = $payment->user_id;

        // Check if user already has an active subscription
        $existingSubscription = UserSubscription::where('user_id', $userId)
            ->where('is_active', true)
            ->first();

        if ($existingSubscription) {
            // Extend the existing subscription
            $existingSubscription->end_date = Carbon::parse($existingSubscription->end_date)
                ->addDays($tariff->duration_days);
            $existingSubscription->save();

            Log::info('Extended existing subscription', [
                'user_id' => $userId,
                'subscription_id' => $existingSubscription->id,
                'new_end_date' => $existingSubscription->end_date
            ]);
        } else {
            // Create a new subscription
            $subscription = new UserSubscription();
            $subscription->user_id = $userId;
            $subscription->tariff_id = $tariff->id;
            $subscription->start_date = now();
            $subscription->end_date = now()->addDays($tariff->duration_days);
            $subscription->is_active = true;
            $subscription->auto_renew = false;
            $subscription->save();

            Log::info('Created new subscription', [
                'user_id' => $userId,
                'subscription_id' => $subscription->id,
                'end_date' => $subscription->end_date
            ]);
        }
    }
}
