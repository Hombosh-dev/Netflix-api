<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Gender;
use App\Enums\Role;
use App\Models\CommentLike;
use App\Models\CommentReport;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserListType;

/**
 *
 *
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property Role $role
 * @property Gender|null $gender
 * @property string $id
 * @property string|null $avatar
 * @property string|null $backdrop
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $birthday
 * @property bool $allow_adult
 * @property string|null $last_seen_at
 * @property bool $is_auto_next
 * @property bool $is_auto_play
 * @property bool $is_auto_skip_intro
 * @property bool $is_private_favorites
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CommentLike> $commentLikes
 * @property-read int|null $comment_likes_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CommentReport> $commentReports
 * @property-read int|null $comment_reports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Movie> $movieNotifications
 * @property-read int|null $movie_notifications_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Selection> $selections
 * @property-read int|null $selections_count
 * @method static Builder<static>|User allowedAdults()
 * @method static Builder<static>|User byRole(\App\Enums\Role $role)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User isAdmin()
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User vipCustomer()
 * @method static Builder<static>|User whereAllowAdult($value)
 * @method static Builder<static>|User whereAvatar($value)
 * @method static Builder<static>|User whereBackdrop($value)
 * @method static Builder<static>|User whereBirthday($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereDescription($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereGender($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereIsAutoNext($value)
 * @method static Builder<static>|User whereIsAutoPlay($value)
 * @method static Builder<static>|User whereIsAutoSkipIntro($value)
 * @method static Builder<static>|User whereIsPrivateFavorites($value)
 * @method static Builder<static>|User whereLastSeenAt($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereRole($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUlids, Notifiable;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function scopeAllowedAdults(Builder $query): Builder
    {
        return $query->where('allow_adult', true);
    }

    public function scopeByRole(Builder $query, Role $role): Builder
    {
        return $query->where('role', $role->value);
    }

    public function scopeIsAdmin(Builder $query): Builder
    {
        return $query->where('role', Role::ADMIN->value);
    }

    public function scopeVipCustomer(Builder $query): Builder
    {
        return $query->where('vip', true);
    }

    public function movieNotifications()
    {
        return $this->belongsToMany(Movie::class, 'movie_user_notifications')
            ->as('notification')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->chaperone();
    }

    public function commentLikes(): HasMany
    {
        return $this->hasMany(CommentLike::class)->chaperone();
    }

    public function commentReports(): HasMany
    {
        return $this->hasMany(CommentReport::class)->chaperone();
    }


    public function selections(): HasMany
    {
        return $this->HasMany(Selection::class)->chaperone();
    }

    public function favoriteMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::FAVORITE->value);
    }


    public function favoritePeople(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', People::class)
            ->where('type', UserListType::FAVORITE->value);
    }

    public function favoriteTags(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Tags::class)
            ->where('type', UserListType::FAVORITE->value);
    }

    public function watchingMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::WATCHING->value);
    }

    public function plannedMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::PLANNED->value);
    }

    public function watchedMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::WATCHED->value);
    }

    public function stoppedMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::STOPPED->value);
    }

    public function reWatchingMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::REWATCHING->value);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function isAdmin(): bool
    {
        return $this->role == Role::ADMIN;
    }

    /**
     * Check if the user has a given role.
     *
     * @param \App\Enums\Role|string $role
     * @return bool
     */
    public function hasRole(Role|string $role): bool
    {
        if (is_string($role)) {
            return $this->role->value === $role;
        }
        return $this->role === $role;
    }

    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'gender' => Gender::class,
            'email_verified_at' => 'datetime',
            'birthday' => 'date',
            'password' => 'hashed',
        ];
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? asset("storage/$value") : null
        );
    }
}
