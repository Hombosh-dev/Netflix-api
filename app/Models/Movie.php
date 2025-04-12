<?php

namespace App\Models;

use App\Enums\Kind;
use App\Enums\Status;
use App\Models\Traits\HasSeo;
use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

/**
 * @mixin IdeHelperMovie
 */
class Movie extends Model
{
    /** @use HasFactory<MovieFactory> */
    use HasFactory, HasUlids, HasSeo;

    protected $hidden = ['searchable'];

    protected function casts(): array
    {
        return [
            'aliases' => AsCollection::class,
            'countries' => 'array',
            'attachments' => 'array',
            'related' => 'array',
            'similars' => AsCollection::class,
            'imdb_score' => 'float',
            'first_air_date' => 'date',
            'last_air_date' => 'date',
            'api_sources' => 'array',
            'kind' => Kind::class,
            'status' => Status::class,
        ];
    }

    public function studio(): BelongsTo
    {
        return $this->belongsTo(Studio::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class)->chaperone();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function persons(): BelongsToMany
    {
        return $this->belongsToMany(Person::class)
            ->withPivot('character_name', 'voice_person_id');
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class)->chaperone();
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function selections(): MorphToMany
    {
        return $this->morphToMany(Selection::class, 'selectionable');
    }


    // Фільтрує за типом (Kind)
    public function scopeOfKind(Builder $query, Kind $kind): Builder
    {
        return $query->where('kind', $kind->value);
    }

    public function scopeWithStatus(Builder $query, Status $status): Builder
    {
        return $query->where('status', $status->value);
    }

    public function scopeWithImdbScoreGreaterThan(Builder $query, float $score): Builder
    {
        return $query->where('imdb_score', '>=', $score);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query
            ->select('*')
            ->addSelect(DB::raw("ts_rank(searchable, websearch_to_tsquery('ukrainian', ?)) AS rank"))
            ->addSelect(DB::raw("ts_headline('ukrainian', name, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS name_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', aliases, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS aliases_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', description, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS description_highlight"))
            ->addSelect(DB::raw('similarity(name, ?) AS similarity'))
            ->whereRaw("searchable @@ websearch_to_tsquery('ukrainian', ?)",
                [$search, $search, $search, $search, $search, $search])
            ->orWhereRaw('name % ?', [$search])
            ->orderByDesc('rank')
            ->orderByDesc('similarity');
    }
}
