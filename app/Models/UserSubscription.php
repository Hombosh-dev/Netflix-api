<?php

namespace App\Models;

use App\Models\Builders\UserSubscriptionQueryBuilder;
use Database\Factories\UserSubscriptionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * @mixin IdeHelperUserSubscription
 */
class UserSubscription extends Model
{
    /** @use HasFactory<UserSubscriptionFactory> */
    use HasFactory, HasUlids;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return UserSubscriptionQueryBuilder
     */
    public function newEloquentBuilder($query): UserSubscriptionQueryBuilder
    {
        return new UserSubscriptionQueryBuilder($query);
    }
    
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
            'auto_renew' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    public function daysLeft(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->end_date);
    }
}
