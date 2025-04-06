<?php

namespace App\Models;

use App\Models\Traits\HasSeo;
use Database\Factories\TagsFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * 
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string|null $image
 * @property \Illuminate\Support\Collection $aliases
 * @property bool $is_genre
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \Database\Factories\TagsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags genres()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags search(string $term)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereAliases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereIsGenre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tags extends Model
{
    /** @use HasFactory<TagsFactory> */
    use HasFactory, HasUlids;

    public function scopeGenres($query)
    {
        return $query->where('is_genre', true);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'LIKE', "%{$term}%")
            ->orWhere('slug', 'LIKE', "%{$term}%");
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class);
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    protected function casts(): array
    {
        return [
            'aliases' => AsCollection::class,
        ];
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? url("storage/$value") : null
        );
    }
}
