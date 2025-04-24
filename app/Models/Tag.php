<?php

namespace App\Models;

use App\Interfaces\Listable;
use App\Models\Builders\TagQueryBuilder;
use App\Models\Traits\HasSearchable;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasFiles;
use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Query\Builder;

/**
 * Tag model representing movie genres and tags.
 *
 * @mixin IdeHelperTag
 */
class Tag extends Model implements Listable
{
    /** @use HasFactory<TagFactory> */
    use HasFactory, HasUlids, HasSeo, HasSearchable, HasFiles;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'searchable',
    ];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return TagQueryBuilder
     */
    public function newEloquentBuilder($query): TagQueryBuilder
    {
        return new TagQueryBuilder($query);
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
            'is_genre' => 'boolean',
        ];
    }


    /**
     * Get the movies associated with the tag.
     *
     * @return BelongsToMany
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class);
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
     * Get the image URL attribute.
     *
     * @return Attribute
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFileUrl($this->image)
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
     * Get the movies count attribute.
     *
     * @return Attribute
     */
    protected function moviesCount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->movies()->count()
        );
    }
}
