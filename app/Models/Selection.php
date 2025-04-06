<?php

namespace App\Models;

use App\Models\Traits\HasSeo;
use Database\Factories\SelectionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @property string $id
 * @property string $user_id
 * @property string $slug
 * @property string $name
 * @property string|null $description
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\People> $persons
 * @property-read int|null $persons_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static \Database\Factories\SelectionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Selection newModelQuery()
 * @method static Builder<static>|Selection newQuery()
 * @method static Builder<static>|Selection query()
 * @method static Builder<static>|Selection search(string $search)
 * @method static Builder<static>|Selection whereCreatedAt($value)
 * @method static Builder<static>|Selection whereDescription($value)
 * @method static Builder<static>|Selection whereId($value)
 * @method static Builder<static>|Selection whereMetaDescription($value)
 * @method static Builder<static>|Selection whereMetaImage($value)
 * @method static Builder<static>|Selection whereMetaTitle($value)
 * @method static Builder<static>|Selection whereName($value)
 * @method static Builder<static>|Selection whereSlug($value)
 * @method static Builder<static>|Selection whereUpdatedAt($value)
 * @method static Builder<static>|Selection whereUserId($value)
 * @mixin \Eloquent
 */
class Selection extends Model
{
    /** @use HasFactory<SelectionFactory> */
    use HasFactory, HasUlids;

    protected $hidden = ['searchable'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movies(): MorphToMany
    {
        return $this->morphedByMany(Movie::class, 'selectionable');
    }

    public function persons(): MorphToMany
    {
        return $this->morphedByMany(People::class, 'selectionable');
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
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
