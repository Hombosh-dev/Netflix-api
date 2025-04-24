<?php

namespace App\Models\Builders;

use App\Enums\PaymentStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentQueryBuilder extends Builder
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
     * Filter by subscription.
     *
     * @param string $subscriptionId
     * @return self
     */
    public function forSubscription(string $subscriptionId): self
    {
        return $this->where('user_subscription_id', $subscriptionId);
    }
    
    /**
     * Filter by status.
     *
     * @param PaymentStatus $status
     * @return self
     */
    public function withStatus(PaymentStatus $status): self
    {
        return $this->where('status', $status->value);
    }
    
    /**
     * Get successful payments.
     *
     * @return self
     */
    public function successful(): self
    {
        return $this->withStatus(PaymentStatus::SUCCESS);
    }
    
    /**
     * Get failed payments.
     *
     * @return self
     */
    public function failed(): self
    {
        return $this->withStatus(PaymentStatus::FAILED);
    }
    
    /**
     * Get pending payments.
     *
     * @return self
     */
    public function pending(): self
    {
        return $this->withStatus(PaymentStatus::PENDING);
    }
    
    /**
     * Get payments in a date range.
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return self
     */
    public function inDateRange(Carbon $startDate, Carbon $endDate): self
    {
        return $this->whereBetween('created_at', [$startDate, $endDate]);
    }
    
    /**
     * Get payments with amount greater than.
     *
     * @param float $amount
     * @return self
     */
    public function withAmountGreaterThan(float $amount): self
    {
        return $this->where('amount', '>=', $amount);
    }
}
