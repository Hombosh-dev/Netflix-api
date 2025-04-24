<?php

namespace App\Models;

use App\Models\Builders\StudioQueryBuilder;
use App\Models\Traits\HasSearchable;
use App\Models\Traits\HasSeo;
use App\Models\Traits\HasFiles;
use Database\Factories\StudioFactory;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperStudio
 */
class Studio extends Model
{
    /** @use HasFactory<StudioFactory> */
    use HasFactory, HasUlids, HasSeo, HasSearchable, HasFiles;

    protected $hidden = [
        'searchable',
    ];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return StudioQueryBuilder
     */
    public function newEloquentBuilder($query): StudioQueryBuilder
    {
        return new StudioQueryBuilder($query);
    }

    protected function casts(): array
    {
        return [
            'aliases' => AsCollection::class,
        ];
    }

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
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
     * Generate a unique slug for a studio name.
     *
     * @param string $name
     * @return string
     */
    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        // Check if the slug already exists
        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Generate a meta title for a studio.
     *
     * @param string $name
     * @return string
     */
    public static function makeMetaTitle(string $name): string
    {
        return $name . ' | ' . config('app.name');
    }
}
