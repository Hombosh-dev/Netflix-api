<?php

namespace App\Models;

use App\Models\Builders\SelectionQueryBuilder;
use App\Models\Traits\HasSearchable;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasFiles;
use Database\Factories\SelectionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperSelection
 */
class Selection extends Model
{
    /** @use HasFactory<SelectionFactory> */
    use HasFactory, HasUlids, HasSeo, HasSearchable, HasFiles;

    protected $hidden = ['searchable'];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return SelectionQueryBuilder
     */
    public function newEloquentBuilder($query): SelectionQueryBuilder
    {
        return new SelectionQueryBuilder($query);
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

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
        return $this->morphedByMany(Person::class, 'selectionable');
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
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
}
