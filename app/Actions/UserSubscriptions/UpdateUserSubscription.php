<?php

namespace App\Actions\UserSubscriptions;

use App\DTOs\UserSubscriptions\UserSubscriptionUpdateDTO;
use App\Models\Tariff;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserSubscription
{
    use AsAction;

    /**
     * Update an existing user subscription.
     *
     * @param  UserSubscription  $userSubscription
     * @param  UserSubscriptionUpdateDTO  $dto
     * @return UserSubscription
     */
    public function handle(UserSubscription $userSubscription, UserSubscriptionUpdateDTO $dto): UserSubscription
    {
        // Update the user subscription
        if ($dto->tariffId !== null) {
            $userSubscription->tariff_id = $dto->tariffId;
        }
        
        if ($dto->startDate !== null) {
            $userSubscription->start_date = $dto->startDate;
        }
        
        if ($dto->endDate !== null) {
            $userSubscription->end_date = $dto->endDate;
        }
        
        if ($dto->isActive !== null) {
            $userSubscription->is_active = $dto->isActive;
        }
        
        if ($dto->autoRenew !== null) {
            $userSubscription->auto_renew = $dto->autoRenew;
        }
        
        $userSubscription->save();
        
        return $userSubscription->load(['user', 'tariff']);
    }
}
