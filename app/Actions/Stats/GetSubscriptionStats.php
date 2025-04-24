<?php

namespace App\Actions\Stats;

use App\DTOs\Stats\StatsDTO;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class GetSubscriptionStats
{
    use AsAction;

    /**
     * Get subscription statistics.
     *
     * @param  StatsDTO  $dto
     * @return array
     */
    public function handle(StatsDTO $dto): array
    {
        $days = $dto->days;
        
        $totalSubscriptions = UserSubscription::count();
        $activeSubscriptions = UserSubscription::where('is_active', true)->count();
        $expiredSubscriptions = UserSubscription::where('is_active', false)->count();
        $newSubscriptionsToday = UserSubscription::whereDate('created_at', Carbon::today())->count();
        $newSubscriptionsThisWeek = UserSubscription::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $newSubscriptionsThisMonth = UserSubscription::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $newSubscriptionsThisPeriod = UserSubscription::where('created_at', '>=', Carbon::now()->subDays($days))->count();

        return [
            'total' => $totalSubscriptions,
            'active' => $activeSubscriptions,
            'expired' => $expiredSubscriptions,
            'new_today' => $newSubscriptionsToday,
            'new_this_week' => $newSubscriptionsThisWeek,
            'new_this_month' => $newSubscriptionsThisMonth,
            'new_this_period' => $newSubscriptionsThisPeriod,
        ];
    }
}
