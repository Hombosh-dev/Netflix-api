<?php

namespace App\Models;

use Database\Factories\StudioFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @property string $slug
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string|null $image
 * @property string|null $aliases
 * @property bool $is_genre
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @method static Builder<static>|Studio byName(string $name)
 * @method static \Database\Factories\StudioFactory factory($count = null, $state = [])
 * @method static Builder<static>|Studio newModelQuery()
 * @method static Builder<static>|Studio newQuery()
 * @method static Builder<static>|Studio query()
 * @method static Builder<static>|Studio search(string $search)
 * @method static Builder<static>|Studio whereAliases($value)
 * @method static Builder<static>|Studio whereCreatedAt($value)
 * @method static Builder<static>|Studio whereDescription($value)
 * @method static Builder<static>|Studio whereId($value)
 * @method static Builder<static>|Studio whereImage($value)
 * @method static Builder<static>|Studio whereIsGenre($value)
 * @method static Builder<static>|Studio whereMetaDescription($value)
 * @method static Builder<static>|Studio whereMetaImage($value)
 * @method static Builder<static>|Studio whereMetaTitle($value)
 * @method static Builder<static>|Studio whereName($value)
 * @method static Builder<static>|Studio whereSlug($value)
 * @method static Builder<static>|Studio whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Studio extends Model
{
    /** @use HasFactory<StudioFactory> */
    use HasFactory, HasUlids;
    protected $fillable = [
        'slug',
        'meta_title',
        'meta_description',
        'meta_image',
        'name',
        'description',
        'image',
        'aliases',
        'is_genre',
        // Do NOT include "searchable"
    ];

    protected $hidden = [
        'searchable',
    ];

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }

    // TODO: fulltext search
    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', '%'.$name.'%');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query
            ->select('*')
            ->addSelect(DB::raw("ts_rank(searchable, websearch_to_tsquery('ukrainian', ?)) AS rank"))
            ->addSelect(DB::raw("ts_headline('ukrainian', name, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS name_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', description, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS description_highlight"))
            ->addSelect(DB::raw('similarity(name, ?) AS similarity'))
            ->whereRaw("searchable @@ websearch_to_tsquery('ukrainian', ?)", [$search, $search, $search, $search, $search])
            ->orWhereRaw('name % ?', [$search])
            ->orderByDesc('rank')
            ->orderByDesc('similarity');
    }

    /**
     * Create a new Studio record.
     *
     * @param array $data
     * @return Studio
     */
    public static function createStudio(array $data): Studio
    {
        return self::create($data);
    }

    /**
     * Retrieve a Studio record by its ID.
     *
     * @param string $id
     * @return Studio|null
     */
    public static function readStudio(string $id): ?Studio
    {
        return self::find($id);
    }

    /**
     * Update the current Studio record with new data.
     *
     * @param array $data
     * @return bool
     */
    public function updateStudio(array $data): bool
    {
        return $this->update($data);
    }

    /**
     * Delete the current Studio record.
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deleteStudio(): ?bool
    {
        return $this->delete();
    }
}
