<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\PersonType;
use App\Interfaces\Listable;
use App\Interfaces\Selectionable;
use App\Models\Builders\PersonQueryBuilder;
use App\Models\Traits\HasSearchable;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasFiles;
use Database\Factories\PersonFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Query\Builder;


/**
 * @mixin IdeHelperPerson
 */
class Person extends Model implements Selectionable, Listable
{
    /** @use HasFactory<PersonFactory> */
    use HasFactory, HasUlids, HasSeo, HasSearchable, HasFiles;

    protected $hidden = [
        'searchable',
    ];

    protected function casts(): array
    {
        return [
            'type' => PersonType::class,
            'gender' => Gender::class,
            'birthday' => 'date',
        ];
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return PersonQueryBuilder
     */
    public function newEloquentBuilder($query): PersonQueryBuilder
    {
        return new PersonQueryBuilder($query);
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

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->original_name
                ? "{$this->name} ({$this->original_name})"
                : $this->name,
        );
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->birthday) {
                    return null;
                }

                // Перевіряємо, що дата народження не в майбутньому
                if ($this->birthday->isAfter(now())) {
                    return 0;
                }

                // Використовуємо abs для гарантії позитивного числа та intval для цілого числа
                return intval(abs($this->birthday->diffInYears(now())));
            }
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
}
