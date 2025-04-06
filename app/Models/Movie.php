<?php

namespace App\Models;

use Database\Factories\MovieFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $api_sources
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property string $image_name
 * @property string $aliases
 * @property string $studio_id
 * @property string $countries
 * @property string|null $poster
 * @property int|null $duration
 * @property int|null $episodes_count
 * @property string|null $first_air_date
 * @property string|null $last_air_date
 * @property string|null $imdb_score
 * @property string $attachments
 * @property string $related
 * @property string $similars
 * @property bool $is_published
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $kind
 * @property-read \App\Models\Studio $studio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereAliases($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereApiSources($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereAttachments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereCountries($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereEpisodesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereFirstAirDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereImageName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereImdbScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereIsPublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereKind($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereLastAirDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereMetaDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereMetaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereMetaTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie wherePoster($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereRelated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereSimilars($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereStudioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Movie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Movie extends Model
{
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
    ];

    protected $hidden = [
        'searchable',
    ];
    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Database\Factories\MovieFactory
     */
    public static function factory(...$parameters): MovieFactory
    {
        return MovieFactory::new(...$parameters);
    }

}
