<?php

namespace App\Actions\UserSubscriptions;

use App\DTOs\UserSubscriptions\UserSubscriptionStoreDTO;
use App\Models\Tariff;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateUserSubscription
{
    use AsAction;

    /**
     * Create a new user subscription.
     *
     * @param  UserSubscriptionStoreDTO  $dto
     * @return UserSubscription
     */
    public function handle(UserSubscriptionStoreDTO $dto): UserSubscription
    {
        // Create new user subscription
        $userSubscription = new UserSubscription();
        $userSubscription->user_id = $dto->userId;
        $userSubscription->tariff_id = $dto->tariffId;
        $userSubscription->start_date = $dto->startDate;
        $userSubscription->end_date = $dto->endDate;
        $userSubscription->is_active = $dto->isActive;
        $userSubscription->auto_renew = $dto->autoRenew;
        $userSubscription->save();
        
        return $userSubscription->load(['user', 'tariff']);
    }
}
