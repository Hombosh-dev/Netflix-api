<?php

use App\Enums\Gender;
use App\Enums\Role;
use App\Enums\UserListType;
use App\Models\Builders\UserQueryBuilder;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\CommentReport;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Payment;
use App\Models\Person;
use App\Models\Rating;
use App\Models\Selection;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserList;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі User
test('user has correct query builder', function () {
    expect(User::query())->toBeInstanceOf(UserQueryBuilder::class);
});

test('user has correct relationships', function () {
    $user = new User();

    expect($user->ratings())->toBeInstanceOf(HasMany::class)
        ->and($user->comments())->toBeInstanceOf(HasMany::class)
        ->and($user->commentLikes())->toBeInstanceOf(HasMany::class)
        ->and($user->commentReports())->toBeInstanceOf(HasMany::class)
        ->and($user->selections())->toBeInstanceOf(HasMany::class)
        ->and($user->userLists())->toBeInstanceOf(HasMany::class)
        ->and($user->subscriptions())->toBeInstanceOf(HasMany::class)
        ->and($user->payments())->toBeInstanceOf(HasMany::class)
        ->and($user->favoriteMovies())->toBeInstanceOf(HasMany::class)
        ->and($user->favoritePeople())->toBeInstanceOf(HasMany::class)
        ->and($user->favoriteTags())->toBeInstanceOf(HasMany::class)
        ->and($user->favoriteEpisodes())->toBeInstanceOf(HasMany::class)
        ->and($user->watchingMovies())->toBeInstanceOf(HasMany::class)
        ->and($user->plannedMovies())->toBeInstanceOf(HasMany::class)
        ->and($user->watchedMovies())->toBeInstanceOf(HasMany::class)
        ->and($user->stoppedMovies())->toBeInstanceOf(HasMany::class)
        ->and($user->reWatchingMovies())->toBeInstanceOf(HasMany::class);
});

test('user has correct casts', function () {
    $user = new User();
    $casts = $user->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKeys([
            'role', 'gender', 'email_verified_at', 'birthday', 'password',
            'allow_adult', 'is_auto_next', 'is_auto_play', 'is_auto_skip_intro',
            'is_private_favorites', 'is_banned', 'last_seen_at'
        ])
        ->and($casts['role'])->toBe(Role::class)
        ->and($casts['gender'])->toBe(Gender::class)
        ->and($casts['email_verified_at'])->toBe('datetime')
        ->and($casts['birthday'])->toBe('date')
        ->and($casts['password'])->toBe('hashed')
        ->and($casts['allow_adult'])->toBe('boolean')
        ->and($casts['is_banned'])->toBe('boolean')
        ->and($casts['last_seen_at'])->toBe('datetime');
});

test('user has correct accessors', function () {
    $user = new User([
        'name' => 'Test User',
        'avatar' => 'avatars/test.jpg',
        'backdrop' => 'backdrops/test.jpg',
        'birthday' => Carbon::now()->subYears(25),
        'last_seen_at' => Carbon::now()->subMinutes(2),
    ]);

    expect($user->avatar)->toContain('storage/avatars/test.jpg')
        ->and($user->backdropUrl)->toContain('storage/backdrops/test.jpg')
        ->and($user->age)->toBe(25)
        ->and($user->formattedLastSeen)->toContain('Сьогодні')
        ->and($user->isOnline)->toBeTrue();
});

test('user query builder filters by role', function () {
    // Arrange
    $role = Role::ADMIN;

    // Act
    $query = User::byRole($role);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"role" = ?')
        ->and($query->getBindings())->toContain($role->value);
});

test('user query builder can get admins', function () {
    // Act
    $query = User::admins();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"role" = ?')
        ->and($query->getBindings())->toContain(Role::ADMIN->value);
});

test('user query builder can get moderators', function () {
    // Act
    $query = User::moderators();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"role" = ?')
        ->and($query->getBindings())->toContain(Role::MODERATOR->value);
});

test('user query builder can get users who allow adult content', function () {
    // Act
    $query = User::allowedAdults();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"allow_adult" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('user query builder can get active users', function () {
    // Arrange
    $days = 30;

    // Act
    $query = User::active($days);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"last_seen_at" >= ?');
});

test('user query builder can get inactive users', function () {
    // Arrange
    $days = 30;

    // Act
    $query = User::inactive($days);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"last_seen_at" < ?');
});

test('user query builder can get users with active subscriptions', function () {
    // Act
    $query = User::withActiveSubscriptions();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('"is_active" = ?');
});

test('user query builder can get users with expired subscriptions', function () {
    // Act
    $query = User::withExpiredSubscriptions();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('"end_date" < ?');
});

test('user query builder can get users with auto-renewable subscriptions', function () {
    // Act
    $query = User::withAutoRenewableSubscriptions();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('"auto_renew" = ?');
});

test('user query builder can get users by age range', function () {
    // Arrange
    $minAge = 18;
    $maxAge = 30;

    // Act
    $query = User::byAgeRange($minAge, $maxAge);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"birthday" between ? and ?');
});

test('user query builder can get users with specific settings', function () {
    // Arrange
    $isAutoNext = true;
    $isAutoPlay = false;
    $isAutoSkipIntro = true;

    // Act
    $query = User::withSettings($isAutoNext, $isAutoPlay, $isAutoSkipIntro);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_auto_next" = ?')
        ->and($sql)->toContain('"is_auto_play" = ?')
        ->and($sql)->toContain('"is_auto_skip_intro" = ?')
        ->and($query->getBindings())->toContain(true)
        ->and($query->getBindings())->toContain(false)
        ->and($query->getBindings())->toContain(true);
});

test('user query builder can get banned users', function () {
    // Act
    $query = User::banned();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_banned" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('user query builder can get non-banned users', function () {
    // Act
    $query = User::notBanned();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_banned" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('user factory creates valid model', function () {
    // Act
    $user = User::factory()->make();

    // Assert
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->not->toBeEmpty()
        ->and($user->email)->not->toBeEmpty()
        ->and($user->password)->not->toBeEmpty();
});

// Додаємо тест для створення користувача в базі даних
test('can create user in database', function () {
    // Act
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => Role::USER,
        'gender' => Gender::MALE,
        'birthday' => '1990-01-01',
        'allow_adult' => true,
        'is_auto_next' => true,
        'is_auto_play' => false,
        'is_auto_skip_intro' => true,
        'is_private_favorites' => false,
        'is_banned' => false,
        'last_seen_at' => now(),
    ]);

    // Assert
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->exists)->toBeTrue()
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com')
        ->and($user->role)->toBe(Role::USER)
        ->and($user->gender)->toBe(Gender::MALE)
        ->and($user->birthday->format('Y-m-d'))->toBe('1990-01-01')
        ->and($user->allow_adult)->toBeTrue()
        ->and($user->is_auto_next)->toBeTrue()
        ->and($user->is_auto_play)->toBeFalse()
        ->and($user->is_auto_skip_intro)->toBeTrue()
        ->and($user->is_private_favorites)->toBeFalse()
        ->and($user->is_banned)->toBeFalse();

    // Перевіряємо, що користувач дійсно збережений в базі даних
    expect(User::where('id', $user->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => Role::USER->value,
        'gender' => Gender::MALE->value,
        'allow_adult' => true,
        'is_auto_next' => true,
        'is_auto_play' => false,
        'is_auto_skip_intro' => true,
        'is_private_favorites' => false,
        'is_banned' => false,
    ]);
});

// Додаємо тест для перевірки методів isAdmin та isModerator
test('user can check roles', function () {
    // Arrange
    $admin = User::factory()->create(['role' => Role::ADMIN]);
    $moderator = User::factory()->create(['role' => Role::MODERATOR]);
    $user = User::factory()->create(['role' => Role::USER]);

    // Act & Assert
    expect($admin->isAdmin())->toBeTrue()
        ->and($admin->isModerator())->toBeFalse()
        ->and($admin->hasRole(Role::ADMIN))->toBeTrue()
        ->and($admin->hasRole('admin'))->toBeTrue()
        ->and($admin->hasRole(Role::MODERATOR))->toBeFalse();

    expect($moderator->isAdmin())->toBeFalse()
        ->and($moderator->isModerator())->toBeTrue()
        ->and($moderator->hasRole(Role::MODERATOR))->toBeTrue()
        ->and($moderator->hasRole('moderator'))->toBeTrue()
        ->and($moderator->hasRole(Role::ADMIN))->toBeFalse();

    expect($user->isAdmin())->toBeFalse()
        ->and($user->isModerator())->toBeFalse()
        ->and($user->hasRole(Role::USER))->toBeTrue()
        ->and($user->hasRole('user'))->toBeTrue();
});

// Додаємо тест для перевірки зв'язків з іншими моделями
test('user can have related models', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Створюємо пов'язані моделі
    $rating = Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
    ]);

    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $userList = UserList::factory()->create([
        'user_id' => $user->id,
        'listable_id' => $movie->id,
        'listable_type' => Movie::class,
        'type' => UserListType::FAVORITE,
    ]);

    // Act & Assert
    expect($user->ratings)->toHaveCount(1)
        ->and($user->ratings->first()->id)->toBe($rating->id)
        ->and($user->comments)->toHaveCount(1)
        ->and($user->comments->first()->id)->toBe($comment->id)
        ->and($user->userLists)->toHaveCount(1)
        ->and($user->userLists->first()->id)->toBe($userList->id)
        ->and($user->favoriteMovies)->toHaveCount(1)
        ->and($user->favoriteMovies->first()->id)->toBe($userList->id);
});
