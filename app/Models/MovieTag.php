<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $movie_id
 * @property string $tag_id
 * @property-read \App\Models\Movie $movie
 * @property-read \App\Models\Tags $tag
 * @method static \Database\Factories\MovieTagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieTag whereTagId($value)
 * @mixin \Eloquent
 */
class MovieTag extends Model
{
    /** @use HasFactory<\Database\Factories\MovieTagFactory> */
    use HasFactory;
    protected $table = 'movie_tag';

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tags::class);
    }
}
