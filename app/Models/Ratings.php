<?php

namespace App\Models;

use Database\Factories\RatingsFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 
 *
 * @property string $id
 * @property string $user_id
 * @property string $movie_id
 * @property int $number
 * @property mixed|null $review
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Movie $movie
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Ratings betweenRatings(int $minRating, int $maxRating)
 * @method static \Database\Factories\RatingsFactory factory($count = null, $state = [])
 * @method static Builder<static>|Ratings forMovie(string $movieId)
 * @method static Builder<static>|Ratings forUser(string $userId)
 * @method static Builder<static>|Ratings newModelQuery()
 * @method static Builder<static>|Ratings newQuery()
 * @method static Builder<static>|Ratings query()
 * @method static Builder<static>|Ratings whereCreatedAt($value)
 * @method static Builder<static>|Ratings whereId($value)
 * @method static Builder<static>|Ratings whereMovieId($value)
 * @method static Builder<static>|Ratings whereNumber($value)
 * @method static Builder<static>|Ratings whereReview($value)
 * @method static Builder<static>|Ratings whereUpdatedAt($value)
 * @method static Builder<static>|Ratings whereUserId($value)
 * @mixin \Eloquent
 */
class Ratings extends Model
{
    /** @use HasFactory<RatingsFactory> */
    use HasFactory, HasUlids;

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
            get: fn (mixed $value, array $attributes) => nl2br(e($attributes['review'])),
            set: fn (mixed $value) => trim($value)
        );
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMovie(Builder $query, string $movieId): Builder
    {
        return $query->where('movie_id', $movieId);
    }

    public function scopeBetweenRatings(Builder $query, int $minRating, int $maxRating): Builder
    {
        return $query->whereBetween('number', [$minRating, $maxRating]);
    }
}
