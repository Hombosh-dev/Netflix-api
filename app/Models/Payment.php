<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Models\Builders\PaymentQueryBuilder;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * @mixin IdeHelperPayment
 */
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory, HasUlids;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return PaymentQueryBuilder
     */
    public function newEloquentBuilder($query): PaymentQueryBuilder
    {
        return new PaymentQueryBuilder($query);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => PaymentStatus::class,
            'liqpay_data' => AsCollection::class,
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

    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::SUCCESS;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatus::PENDING;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }

    public function isRefunded(): bool
    {
        return $this->status === PaymentStatus::REFUNDED;
    }
}
