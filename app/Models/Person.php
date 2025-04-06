<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $movie_id
 * @property string $person_id
 * @property string|null $voice_person_id
 * @property string $character_name
 * @property-read \App\Models\Movie $movie
 * @property-read \App\Models\People $person
 * @property-read \App\Models\People|null $voicePerson
 * @method static \Database\Factories\PersonFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCharacterName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person wherePersonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereVoicePersonId($value)
 * @mixin \Eloquent
 */
class Person extends Model
{
    /** @use HasFactory<\Database\Factories\PersonFactory> */
    use HasFactory, HasUlids;

    /**
     * The table associated with the model.
     */
    protected $table = 'movie_person';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * Since we have a composite primary key (movie_id, person_id) and use ULIDs,
     * we disable auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * The migration does not include timestamp columns.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'movie_id',
        'person_id',
        'voice_person_id',
        'character_name',
    ];

    /**
     * Get the movie that this pivot belongs to.
     */
    public function movie(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Movie::class, 'movie_id');
    }

    /**
     * Get the person associated with this pivot.
     */
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // If your primary people model is called "People"
        // you can adjust the class name as needed.
        return $this->belongsTo(People::class, 'person_id');
    }

    /**
     * Get the voice person if one exists.
     */
    public function voicePerson(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(People::class, 'voice_person_id');
    }
}
