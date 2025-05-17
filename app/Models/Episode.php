<?php

namespace App\Models;

use App\Interfaces\Commentable;
use App\Interfaces\Listable;
use App\Models\Builders\EpisodeQueryBuilder;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasUserInteractions;
use App\Models\Traits\HasFiles;
use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * Episode model representing TV series episodes.
 *
 * @mixin IdeHelperEpisode
 */
class Episode extends Model implements Listable, Commentable
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory, HasUlids, HasSeo, HasUserInteractions, HasFiles;

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return EpisodeQueryBuilder
     */
    public function newEloquentBuilder($query): EpisodeQueryBuilder
    {
        return new EpisodeQueryBuilder($query);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pictures' => AsCollection::class,
            'video_players' => AsCollection::class,
            'air_date' => 'date',
            'is_filler' => 'boolean',
        ];
    }

    /**
     * Get the movie that owns the episode.
     *
     * @return BelongsTo
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
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
     * Get the primary picture URL attribute.
     *
     * @return Attribute
     */
    protected function pictureUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->pictures)) {
                    return null;
                }

                $pictures = $this->pictures;

                // Якщо pictures - це рядок, перетворюємо його в масив
                if (is_string($pictures)) {
                    $pictures = json_decode($pictures, true);
                }

                // Якщо pictures - це колекція, перетворюємо її в масив
                if ($pictures instanceof \Illuminate\Support\Collection) {
                    $pictures = $pictures->toArray();
                }

                if (empty($pictures)) {
                    return null;
                }

                // Отримуємо перше зображення
                $firstPicture = reset($pictures);

                // Повертаємо URL зображення
                return $firstPicture ? Storage::url($firstPicture) : null;
            }
        );
    }

    /**
     * Get all picture URLs attribute.
     *
     * @return Attribute
     */
    protected function picturesUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (empty($this->pictures)) {
                    return [];
                }

                $pictures = is_string($this->pictures) ? json_decode($this->pictures, true) : $this->pictures;

                if (empty($pictures)) {
                    return [];
                }

                return collect($pictures)->map(function ($picture) {
                    return is_string($picture) ? $this->getFileUrl($picture) : null;
                })->filter()->values()->toArray();
            }
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
     * Get the formatted duration attribute.
     *
     * @return Attribute
     */
    protected function formattedDuration(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->duration ? $this->formatDuration($this->duration) : null
        );
    }

    /**
     * Get the full name attribute (with episode number).
     *
     * @return Attribute
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => "Episode {$this->number}: {$this->name}"
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
     * Format a duration in minutes to a human-readable string.
     *
     * @param  int  $duration  Duration in minutes
     * @return string Formatted duration
     */
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
