<?php

namespace App\Models;

use App\Models\Builders\RatingQueryBuilder;
use Database\Factories\RatingFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;

/**
 * @mixin IdeHelperRating
 */
class Rating extends Model
{
    /** @use HasFactory<RatingFactory> */
    use HasFactory, HasUlids;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return RatingQueryBuilder
     */
    public function newEloquentBuilder($query): RatingQueryBuilder
    {
        return new RatingQueryBuilder($query);
    }

    protected function casts(): array
    {
        return [
            'number' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Відношення з фільмом
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function review(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => nl2br(e($attributes['review'])),
            set: fn(mixed $value) => trim($value)
        );
    }
}
