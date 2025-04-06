<?php

namespace App\Models;

use App\Models\Traits\HasSeo;
use Carbon\Carbon;
use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * 
 *
 * @property string $id
 * @property string $movie_id
 * @property int $number
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property int|null $duration
 * @property string|null $air_date
 * @property bool $is_filler
 * @property string $pictures
 * @property string $video_players
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Movie $movie
 * @property-read mixed $picture_url
 * @property-read mixed $pictures_url
 * @method static Builder<static>|Episode airedAfter(\Carbon\Carbon $date)
 * @method static \Database\Factories\EpisodeFactory factory($count = null, $state = [])
 * @method static Builder<static>|Episode forMovie(string $movieId)
 * @method static Builder<static>|Episode newModelQuery()
 * @method static Builder<static>|Episode newQuery()
 * @method static Builder<static>|Episode query()
 * @method static Builder<static>|Episode whereAirDate($value)
 * @method static Builder<static>|Episode whereCreatedAt($value)
 * @method static Builder<static>|Episode whereDescription($value)
 * @method static Builder<static>|Episode whereDuration($value)
 * @method static Builder<static>|Episode whereId($value)
 * @method static Builder<static>|Episode whereIsFiller($value)
 * @method static Builder<static>|Episode whereMetaDescription($value)
 * @method static Builder<static>|Episode whereMetaImage($value)
 * @method static Builder<static>|Episode whereMetaTitle($value)
 * @method static Builder<static>|Episode whereMovieId($value)
 * @method static Builder<static>|Episode whereName($value)
 * @method static Builder<static>|Episode whereNumber($value)
 * @method static Builder<static>|Episode wherePictures($value)
 * @method static Builder<static>|Episode whereSlug($value)
 * @method static Builder<static>|Episode whereUpdatedAt($value)
 * @method static Builder<static>|Episode whereVideoPlayers($value)
 * @mixin \Eloquent
 */
class Episode extends Model
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory, HasUlids;

    public function scopeForMovie(Builder $query, string $movieId): Builder
    {
        return $query->where('movie_id', $movieId);
    }

    public function scopeAiredAfter(Builder $query, Carbon $date): Builder
    {
        return $query->where('air_date', '>=', $date);
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    protected function pictureUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->pictures->isNotEmpty()
                ? asset("storage/{$this->pictures->first()}")
                : null
        );
    }

    protected function picturesUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->pictures->isNotEmpty()
                ? $this->pictures->map(fn ($picture) => asset("storage/{$picture}"))
                : null
        );
    }

    private function formatDuration(int $duration): string
    {
        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        $formatted = [];

        if ($hours > 0) {
            $formatted[] = "{$hours} год";
        }

        if ($minutes > 0) {
            $formatted[] = "{$minutes} хв";
        }

        return implode(' ', $formatted);
    }
}
