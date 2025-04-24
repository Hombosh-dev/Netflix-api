<?php

namespace App\Http\Controllers;

use App\Actions\Payments\CreatePayment;
use App\Actions\Payments\GetPayments;
use App\Actions\Payments\UpdatePayment;
use App\DTOs\Payments\PaymentIndexDTO;
use App\DTOs\Payments\PaymentStoreDTO;
use App\DTOs\Payments\PaymentUpdateDTO;
use App\Http\Requests\Payments\PaymentDeleteRequest;
use App\Http\Requests\Payments\PaymentIndexRequest;
use App\Http\Requests\Payments\PaymentStoreRequest;
use App\Http\Requests\Payments\PaymentUpdateRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PaymentController extends Controller
{
    /**
     * Get paginated list of payments with filtering, sorting and pagination
     *
     * @param  PaymentIndexRequest  $request
     * @param  GetPayments  $action
     * @return AnonymousResourceCollection
     */
    public function index(PaymentIndexRequest $request, GetPayments $action): AnonymousResourceCollection
    {
        $dto = PaymentIndexDTO::fromRequest($request);
        $payments = $action->handle($dto);

        return PaymentResource::collection($payments);
    }

    /**
     * Get detailed information about a specific payment
     *
     * @param  Payment  $payment
     * @return PaymentResource
     */
    public function show(Payment $payment): PaymentResource
    {
        return new PaymentResource($payment->load(['user', 'tariff']));
    }

    /**
     * Store a newly created payment and create a subscription if payment is successful
     *
     * @param  PaymentStoreRequest  $request
     * @param  CreatePayment  $action
     * @return PaymentResource|JsonResponse
     */
    public function store(PaymentStoreRequest $request, CreatePayment $action): PaymentResource|JsonResponse
    {
        $dto = PaymentStoreDTO::fromRequest($request);
        $payment = $action->handle($dto);

        // If payment is successful, create a subscription
        if ($payment->isSuccessful()) {
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
            }
        }

        return new PaymentResource($payment);
    }

    /**
     * Update the specified payment
     *
     * @param  PaymentUpdateRequest  $request
     * @param  Payment  $payment
     * @param  UpdatePayment  $action
     * @return PaymentResource
     */
    public function update(PaymentUpdateRequest $request, Payment $payment, UpdatePayment $action): PaymentResource
    {
        $dto = PaymentUpdateDTO::fromRequest($request);
        $payment = $action->handle($payment, $dto);

        return new PaymentResource($payment);
    }

    /**
     * Remove the specified payment
     *
     * @param  PaymentDeleteRequest  $request
     * @param  Payment  $payment
     * @return JsonResponse
     */
    public function destroy(PaymentDeleteRequest $request, Payment $payment): JsonResponse
    {
        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }

    /**
     * Get payments for a specific user
     *
     * @param  User  $user
     * @param  PaymentIndexRequest  $request
     * @param  GetPayments  $action
     * @return AnonymousResourceCollection
     */
    public function forUser(User $user, PaymentIndexRequest $request, GetPayments $action): AnonymousResourceCollection
    {
        $request->merge(['user_id' => $user->id]);
        $dto = PaymentIndexDTO::fromRequest($request);
        $payments = $action->handle($dto);

        return PaymentResource::collection($payments);
    }
}
