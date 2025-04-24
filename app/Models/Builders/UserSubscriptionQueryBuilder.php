<?php

namespace App\Models\Builders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UserSubscriptionQueryBuilder extends Builder
{
    /**
     * Filter by user.
     *
     * @param string $userId
     * @return self
     */
    public function forUser(string $userId): self
    {
        return $this->where('user_id', $userId);
    }
    
    /**
     * Filter by tariff.
     *
     * @param string $tariffId
     * @return self
     */
    public function forTariff(string $tariffId): self
    {
        return $this->where('tariff_id', $tariffId);
    }
    
    /**
     * Get active subscriptions.
     *
     * @return self
     */
    public function active(): self
    {
        return $this->where('is_active', true);
    }
    
    /**
     * Get inactive subscriptions.
     *
     * @return self
     */
    public function inactive(): self
    {
        return $this->where('is_active', false);
    }
    
    /**
     * Get auto-renewable subscriptions.
     *
     * @return self
     */
    public function autoRenewable(): self
    {
        return $this->where('auto_renew', true);
    }
    
    /**
     * Get non-auto-renewable subscriptions.
     *
     * @return self
     */
    public function nonAutoRenewable(): self
    {
        return $this->where('auto_renew', false);
    }
    
    /**
     * Get subscriptions expiring soon.
     *
     * @param int $days
     * @return self
     */
    public function expiringSoon(int $days = 7): self
    {
        $now = Carbon::now();
        $future = Carbon::now()->addDays($days);
        
        return $this->where('is_active', true)
            ->whereBetween('end_date', [$now, $future]);
    }
    
    /**
     * Get expired subscriptions.
     *
     * @return self
     */
    public function expired(): self
    {
        return $this->where('end_date', '<', Carbon::now())
            ->where('is_active', false);
    }
}
