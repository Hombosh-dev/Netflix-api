<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\PersonType;
use App\Models\Traits\HasSeo;
use Database\Factories\PeopleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

/**
 * 
 *
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string|null $original_name
 * @property string|null $image
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $birthday
 * @property string|null $birthplace
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property PersonType $type
 * @property Gender|null $gender
 * @property-read mixed $age
 * @property-read mixed $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movies
 * @property-read int|null $movies_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserList> $userLists
 * @property-read int|null $user_lists_count
 * @method static Builder<static>|People byGender(string $gender)
 * @method static Builder<static>|People byName(string $name)
 * @method static Builder<static>|People byType(\App\Enums\PersonType $type)
 * @method static \Database\Factories\PeopleFactory factory($count = null, $state = [])
 * @method static Builder<static>|People newModelQuery()
 * @method static Builder<static>|People newQuery()
 * @method static Builder<static>|People query()
 * @method static Builder<static>|People search(string $search)
 * @method static Builder<static>|People whereBirthday($value)
 * @method static Builder<static>|People whereBirthplace($value)
 * @method static Builder<static>|People whereCreatedAt($value)
 * @method static Builder<static>|People whereDescription($value)
 * @method static Builder<static>|People whereGender($value)
 * @method static Builder<static>|People whereId($value)
 * @method static Builder<static>|People whereImage($value)
 * @method static Builder<static>|People whereMetaDescription($value)
 * @method static Builder<static>|People whereMetaImage($value)
 * @method static Builder<static>|People whereMetaTitle($value)
 * @method static Builder<static>|People whereName($value)
 * @method static Builder<static>|People whereOriginalName($value)
 * @method static Builder<static>|People whereSlug($value)
 * @method static Builder<static>|People whereType($value)
 * @method static Builder<static>|People whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class People extends Model
{
    /** @use HasFactory<PeopleFactory> */
    use HasFactory, HasUlids;

    protected $table = 'people';

    public function scopeByType(Builder $query, PersonType $type): Builder
    {
        return $query->where('type', $type->value);
    }

    public function scopeByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'like', '%'.$name.'%');
    }
    public function scopeByGender(Builder $query, string $gender): Builder
    {
        return $query->where('gender', $gender);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query
            ->select('people.*') // Вибираємо колонки з таблиці `people`
            ->addSelect(DB::raw("ts_rank(people.searchable, websearch_to_tsquery('ukrainian', ?)) AS rank"))
            ->addSelect(DB::raw("ts_headline('ukrainian', people.name, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS name_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', people.original_name, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS original_name_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', people.description, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS description_highlight"))
            ->addSelect(DB::raw("ts_headline('ukrainian', movie_person.character_name, websearch_to_tsquery('ukrainian', ?), 'HighlightAll=true') AS character_name_highlight"))
            ->addSelect(DB::raw('similarity(people.name, ?) AS similarity'))
            ->leftJoin('movie_person', 'people.id', '=', 'movie_person.person_id')
            ->whereRaw("people.searchable @@ websearch_to_tsquery('ukrainian', ?)", [$search, $search, $search, $search, $search, $search, $search])
            ->orWhereRaw('people.name % ?', [$search])
            ->orWhereRaw('movie_person.character_name % ?', [$search])
            ->orderByDesc('rank')
            ->orderByDesc('similarity');
    }

    public function movies(): BelongsToMany
    {
        //return $this->belongsToMany(Movie::class, 'movie_person')
        return $this->belongsToMany(Movie::class)
            ->withPivot('character_name');
    }

    public function userLists(): MorphMany
    {
        return $this->morphMany(UserList::class, 'listable');
    }

    public function selections(): MorphToMany
    {
        return $this->morphToMany(Selection::class, 'selectionable');
    }

    protected function casts(): array
    {
        return [
            'type' => PersonType::class,
            'gender' => Gender::class,
            'birthday' => 'date',
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->original_name
                ? "{$this->name} ({$this->original_name})"
                : $this->name,
        );
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->birthday
                ? now()->diffInYears($this->birthday)
                : null,
        );
    }
}
