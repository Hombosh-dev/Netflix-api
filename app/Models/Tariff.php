<?php

namespace App\Models;

use App\Models\Builders\TariffQueryBuilder;
use App\Models\Traits\HasSeo;
use Database\Factories\TariffFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;

/**
 * @mixin IdeHelperTariff
 */
class Tariff extends Model
{
    /** @use HasFactory<TariffFactory> */
    use HasFactory, HasUlids, HasSeo;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return TariffQueryBuilder
     */
    public function newEloquentBuilder($query): TariffQueryBuilder
    {
        return new TariffQueryBuilder($query);
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => AsCollection::class,
            'is_active' => 'boolean',
        ];
    }

    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
