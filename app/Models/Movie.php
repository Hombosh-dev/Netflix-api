<?php

namespace App\Models;

use App\Enums\Kind;
use App\Enums\Status;
use App\Interfaces\Commentable;
use App\Interfaces\Listable;
use App\Interfaces\Selectionable;
use App\Models\Builders\MovieQueryBuilder;
use App\Models\Scopes\PublishedScope;
use App\Models\Traits\HasSearchable;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasUserInteractions;
use App\Models\Traits\HasFiles;
use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;


/**
 * Movie model representing films, TV series, and other video content.
 *
 * @mixin IdeHelperMovie
 */
class Movie extends Model implements Listable, Commentable, Selectionable
{
    /** @use HasFactory<MovieFactory> */
    use HasFactory, HasUlids, HasSeo, HasUserInteractions, HasSearchable, HasFiles;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['searchable'];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new PublishedScope);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return MovieQueryBuilder
     */
    public function newEloquentBuilder($query): MovieQueryBuilder
    {
        return new MovieQueryBuilder($query);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aliases' => AsCollection::class,
            'countries' => AsCollection::class,
            'attachments' => AsCollection::class,
            'related' => AsCollection::class,
            'similars' => AsCollection::class,
            'api_sources' => AsCollection::class,
            'imdb_score' => 'float',
            'first_air_date' => 'date',
            'last_air_date' => 'date',
            'kind' => Kind::class,
            'status' => Status::class,
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get the studio that owns the movie.
     *
     * @return BelongsTo
     */
    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    /**
     * Get the ratings for the movie.
     *
     * @return HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class)->chaperone();
    }

    /**
     * Get the tags associated with the movie.
     *
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the persons associated with the movie.
     *
     * @return BelongsToMany
     */
    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'movie_person', 'movie_id', 'person_id')
            ->withPivot('character_name', 'voice_person_id');
    }

    /**
     * Get the episodes for the movie.
     *
     * @return HasMany
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->chaperone();
    }

    /**
     * Get all user lists associated with this model.
     *
     * @return MorphMany
     */
    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    /**
     * Get all comments associated with this model.
     *
     * @return MorphMany
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get all selections this model belongs to.
     *
     * @return MorphToMany
     */
    public function selections(): MorphToMany
    {
        return $this->morphToMany(Selection::class, 'selectionable');
    }

    // Метод episodes() вже визначений вище

    /**
     * Get the full title attribute (name with year).
     *
     * @return Attribute
     */
    protected function fullTitle(): Attribute
    {
        return Attribute::make(
            get: function () {
                $year = $this->first_air_date ? $this->first_air_date->format('Y') : null;
                return $year ? "{$this->name} ({$year})" : $this->name;
            }
        );
    }

    /**
     * Get the formatted duration attribute.
     *
     * @return Attribute
     */
    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->duration) {
                    return null;
                }

                $hours = floor($this->duration / 60);
                $minutes = $this->duration % 60;

                $formatted = [];

                if ($hours > 0) {
                    $formatted[] = "{$hours} год";
                }

                if ($minutes > 0) {
                    $formatted[] = "{$minutes} хв";
                }

                return implode(' ', $formatted);
            }
        );
    }

    /**
     * Get the poster URL attribute.
     *
     * @return Attribute
     */
    protected function posterUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFileUrl($this->poster)
        );
    }

    /**
     * Get the image URL attribute.
     *
     * @return Attribute
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFileUrl($this->image_name)
        );
    }

    /**
     * Get the meta image URL attribute.
     *
     * @return Attribute
     */
    protected function metaImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFileUrl($this->meta_image)
        );
    }

    /**
     * Get the average rating attribute.
     *
     * @return Attribute
     */
    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: fn() => round($this->ratings()->avg('number') ?? 0, 1)
        );
    }

    /**
     * Get the ratings count attribute.
     *
     * @return Attribute
     */
    protected function ratingsCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->ratings()->count()
        );
    }

    /**
     * Get the comments count attribute.
     *
     * @return Attribute
     */
    protected function commentsCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->comments()->count()
        );
    }

    /**
     * Get the is series attribute.
     *
     * @return Attribute
     */
    protected function isSeries(): Attribute
    {
        return Attribute::make(
            get: fn() => in_array($this->kind, [Kind::TV_SERIES, Kind::ANIMATED_SERIES])
        );
    }

    /**
     * Get the release year attribute.
     *
     * @return Attribute
     */
    protected function releaseYear(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->first_air_date ? $this->first_air_date->format('Y') : null
        );
    }

    /**
     * Get the main genre attribute.
     *
     * @return Attribute
     */
    protected function mainGenre(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->tags()->where('is_genre', true)->first()?->name
        );
    }

    /**
     * Get the main country attribute.
     *
     * @return Attribute
     */
    protected function mainCountry(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->countries->first()
        );
    }
}
