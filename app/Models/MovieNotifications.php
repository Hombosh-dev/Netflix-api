<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property string $user_id
 * @property string $movie_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Movie $movie
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\MovieNotificationsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications whereMovieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MovieNotifications whereUserId($value)
 * @mixin \Eloquent
 */
class MovieNotifications extends Model
{
    /** @use HasFactory<\Database\Factories\MovieNotificationsFactory> */
    use HasFactory;
    protected $table = 'movie_user_notifications';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'movie_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
