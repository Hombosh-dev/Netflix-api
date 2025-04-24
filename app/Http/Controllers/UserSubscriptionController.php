<?php

namespace App\Http\Controllers;

use App\Actions\UserSubscriptions\CreateUserSubscription;
use App\Actions\UserSubscriptions\GetUserSubscriptions;
use App\Actions\UserSubscriptions\UpdateUserSubscription;
use App\DTOs\UserSubscriptions\UserSubscriptionIndexDTO;
use App\DTOs\UserSubscriptions\UserSubscriptionStoreDTO;
use App\DTOs\UserSubscriptions\UserSubscriptionUpdateDTO;
use App\Http\Requests\UserSubscriptions\UserSubscriptionDeleteRequest;
use App\Http\Requests\UserSubscriptions\UserSubscriptionIndexRequest;
use App\Http\Requests\UserSubscriptions\UserSubscriptionStoreRequest;
use App\Http\Requests\UserSubscriptions\UserSubscriptionUpdateRequest;
use App\Http\Resources\UserSubscriptionResource;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserSubscriptionController extends Controller
{
    /**
     * Get paginated list of user subscriptions with filtering, sorting and pagination
     *
     * @param  UserSubscriptionIndexRequest  $request
     * @param  GetUserSubscriptions  $action
     * @return AnonymousResourceCollection
     */
    public function index(UserSubscriptionIndexRequest $request, GetUserSubscriptions $action): AnonymousResourceCollection
    {
        $dto = UserSubscriptionIndexDTO::fromRequest($request);
        $userSubscriptions = $action->handle($dto);

        return UserSubscriptionResource::collection($userSubscriptions);
    }

    /**
     * Get detailed information about a specific user subscription
     *
     * @param  UserSubscription  $userSubscription
     * @return UserSubscriptionResource
     */
    public function show(UserSubscription $userSubscription): UserSubscriptionResource
    {
        return new UserSubscriptionResource($userSubscription->load(['user', 'tariff']));
    }

    /**
     * Store a newly created user subscription
     *
     * @param  UserSubscriptionStoreRequest  $request
     * @param  CreateUserSubscription  $action
     * @return UserSubscriptionResource|JsonResponse
     */
    public function store(UserSubscriptionStoreRequest $request, CreateUserSubscription $action): UserSubscriptionResource|JsonResponse
    {
        // Check if the user already has an active subscription
        $userId = $request->input('user_id', $request->user()->id);
        $existingSubscription = UserSubscription::where('user_id', $userId)
            ->where('is_active', true)
            ->first();
            
        if ($existingSubscription) {
            return response()->json([
                'message' => 'User already has an active subscription',
                'subscription' => new UserSubscriptionResource($existingSubscription->load(['user', 'tariff'])),
            ], 422);
        }

        // If no end_date is provided, calculate it based on the tariff duration
        if (!$request->has('end_date')) {
            $tariff = Tariff::findOrFail($request->input('tariff_id'));
            $startDate = $request->has('start_date') 
                ? Carbon::parse($request->input('start_date')) 
                : now();
            $endDate = $startDate->copy()->addDays($tariff->duration_days);
            $request->merge(['start_date' => $startDate, 'end_date' => $endDate]);
        }

        $dto = UserSubscriptionStoreDTO::fromRequest($request);
        $userSubscription = $action->handle($dto);

        return new UserSubscriptionResource($userSubscription);
    }

    /**
     * Update the specified user subscription
     *
     * @param  UserSubscriptionUpdateRequest  $request
     * @param  UserSubscription  $userSubscription
     * @param  UpdateUserSubscription  $action
     * @return UserSubscriptionResource
     */
    public function update(UserSubscriptionUpdateRequest $request, UserSubscription $userSubscription, UpdateUserSubscription $action): UserSubscriptionResource
    {
        $dto = UserSubscriptionUpdateDTO::fromRequest($request);
        $userSubscription = $action->handle($userSubscription, $dto);

        return new UserSubscriptionResource($userSubscription);
    }

    /**
     * Remove the specified user subscription
     *
     * @param  UserSubscriptionDeleteRequest  $request
     * @param  UserSubscription  $userSubscription
     * @return JsonResponse
     */
    public function destroy(UserSubscriptionDeleteRequest $request, UserSubscription $userSubscription): JsonResponse
    {
        $userSubscription->delete();

        return response()->json([
            'message' => 'Subscription deleted successfully',
        ]);
    }

    /**
     * Get subscriptions for a specific user
     *
     * @param  User  $user
     * @param  UserSubscriptionIndexRequest  $request
     * @param  GetUserSubscriptions  $action
     * @return AnonymousResourceCollection
     */
    public function forUser(User $user, UserSubscriptionIndexRequest $request, GetUserSubscriptions $action): AnonymousResourceCollection
    {
        $request->merge(['user_id' => $user->id]);
        $dto = UserSubscriptionIndexDTO::fromRequest($request);
        $userSubscriptions = $action->handle($dto);

        return UserSubscriptionResource::collection($userSubscriptions);
    }

    /**
     * Get active subscriptions for the authenticated user
     *
     * @param  UserSubscriptionIndexRequest  $request
     * @param  GetUserSubscriptions  $action
     * @return AnonymousResourceCollection
     */
    public function active(UserSubscriptionIndexRequest $request, GetUserSubscriptions $action): AnonymousResourceCollection
    {
        $request->merge([
            'user_id' => $request->user()->id,
            'is_active' => true,
        ]);
        $dto = UserSubscriptionIndexDTO::fromRequest($request);
        $userSubscriptions = $action->handle($dto);

        return UserSubscriptionResource::collection($userSubscriptions);
    }
}
