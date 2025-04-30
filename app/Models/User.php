<?php

namespace App\Models;

use App\Enums\Gender;
use App\Enums\Role;
use App\Enums\UserListType;
use App\Models\Builders\UserQueryBuilder;
use App\Models\Scopes\BannedScope;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Traits\HasFiles;
use Laravel\Sanctum\HasApiTokens;

/**
 * User model representing application users.
 *
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUlids, Notifiable, HasFiles;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  Builder  $query
     * @return UserQueryBuilder
     */
    public function newEloquentBuilder($query): UserQueryBuilder
    {
        return new UserQueryBuilder($query);
    }


    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BannedScope);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => Role::class,
            'gender' => Gender::class,
            'email_verified_at' => 'datetime',
            'birthday' => 'date',
            'password' => 'hashed',
            'allow_adult' => 'boolean',
            'is_auto_next' => 'boolean',
            'is_auto_play' => 'boolean',
            'is_auto_skip_intro' => 'boolean',
            'is_private_favorites' => 'boolean',
            'is_banned' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Get the ratings associated with the user.
     *
     * @return HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class)->chaperone();
    }

    /**
     * Get the comments associated with the user.
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->chaperone();
    }

    /**
     * Get the comment likes associated with the user.
     *
     * @return HasMany
     */
    public function commentLikes(): HasMany
    {
        return $this->hasMany(CommentLike::class)->chaperone();
    }

    /**
     * Get the comment reports associated with the user.
     *
     * @return HasMany
     */
    public function commentReports(): HasMany
    {
        return $this->hasMany(CommentReport::class)->chaperone();
    }

    /**
     * Get the selections associated with the user.
     *
     * @return HasMany
     */
    public function selections(): HasMany
    {
        return $this->hasMany(Selection::class)->chaperone();
    }

    /**
     * Get the user lists associated with the user.
     *
     * @return HasMany
     */
    public function userLists(): HasMany
    {
        return $this->hasMany(UserList::class);
    }

    /**
     * Get the subscriptions associated with the user.
     *
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the payments associated with the user.
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the favorite movies associated with the user.
     *
     * @return HasMany
     */
    public function favoriteMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::FAVORITE->value);
    }

    /**
     * Get the favorite people associated with the user.
     *
     * @return HasMany
     */
    public function favoritePeople(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Person::class)
            ->where('type', UserListType::FAVORITE->value);
    }

    /**
     * Get the favorite tags associated with the user.
     *
     * @return HasMany
     */
    public function favoriteTags(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Tag::class)
            ->where('type', UserListType::FAVORITE->value);
    }

    /**
     * Get the favorite episodes associated with the user.
     *
     * @return HasMany
     */
    public function favoriteEpisodes(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Episode::class)
            ->where('type', UserListType::FAVORITE->value);
    }

    /**
     * Get the watching movies associated with the user.
     *
     * @return HasMany
     */
    public function watchingMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::WATCHING->value);
    }

    /**
     * Get the planned movies associated with the user.
     *
     * @return HasMany
     */
    public function plannedMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::PLANNED->value);
    }

    /**
     * Get the watched movies associated with the user.
     *
     * @return HasMany
     */
    public function watchedMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::WATCHED->value);
    }

    /**
     * Get the stopped movies associated with the user.
     *
     * @return HasMany
     */
    public function stoppedMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::STOPPED->value);
    }

    /**
     * Get the rewatching movies associated with the user.
     *
     * @return HasMany
     */
    public function reWatchingMovies(): HasMany
    {
        return $this->userLists()
            ->where('listable_type', Movie::class)
            ->where('type', UserListType::REWATCHING->value);
    }

    /**
     * Determine if the user can access the given Filament panel.
     *
     * @param  Panel  $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    /**
     * Determine if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role == Role::ADMIN;
    }

    /**
     * Determine if the user is a moderator.
     *
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->role == Role::MODERATOR;
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  Role|string  $role
     * @return bool
     */
    public function hasRole(Role|string $role): bool
    {
        if (is_string($role)) {
            return $this->role->value === $role;
        }
        return $this->role === $role;
    }

    /**
     * Get the avatar URL attribute.
     *
     * @return Attribute
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFileUrl($this->avatar)
        );
    }

    /**
     * Get the backdrop URL attribute.
     *
     * @return Attribute
     */
    protected function backdropUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->getFileUrl($this->backdrop)
        );
    }

    /**
     * Get the age attribute.
     *
     * @return Attribute
     */
    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->birthday ? Carbon::parse($this->birthday)->age : null
        );
    }

    /**
     * Get the formatted last seen attribute.
     *
     * @return Attribute
     */
    protected function formattedLastSeen(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->last_seen_at) {
                    return null;
                }

                $now = Carbon::now();
                $lastSeen = Carbon::parse($this->last_seen_at);

                if ($lastSeen->isToday()) {
                    return 'Сьогодні о '.$lastSeen->format('H:i');
                } elseif ($lastSeen->isYesterday()) {
                    return 'Вчора о '.$lastSeen->format('H:i');
                } elseif ($lastSeen->diffInDays($now) < 7) {
                    return $lastSeen->locale('uk')->dayName.' о '.$lastSeen->format('H:i');
                } else {
                    return $lastSeen->format('d.m.Y H:i');
                }
            }
        );
    }

    /**
     * Get the is online attribute.
     *
     * @return Attribute
     */
    protected function isOnline(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->last_seen_at && Carbon::parse($this->last_seen_at)->diffInMinutes(Carbon::now()) < 5
        );
    }
}
