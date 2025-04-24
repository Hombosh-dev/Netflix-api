<?php

use App\Enums\UserListType;
use App\Models\Builders\UserListQueryBuilder;
use App\Models\Movie;
use App\Models\User;
use App\Models\UserList;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі UserList
test('user list has correct query builder', function () {
    expect(UserList::query())->toBeInstanceOf(UserListQueryBuilder::class);
});

test('user list has correct relationships', function () {
    $userList = new UserList();

    expect($userList->user())->toBeInstanceOf(BelongsTo::class)
        ->and($userList->listable())->toBeInstanceOf(MorphTo::class);
});

test('user list has correct casts', function () {
    $userList = new UserList();
    $casts = $userList->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('type')
        ->and($casts['type'])->toBe(UserListType::class);
});

test('user list query builder can filter by type', function () {
    // Arrange
    $type = UserListType::FAVORITE;

    // Act
    $query = UserList::ofType($type);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain($type->value);
});

test('user list query builder can filter by user', function () {
    // Arrange
    $userId = 'test-user-id';

    // Act
    $query = UserList::forUser($userId);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"user_id" = ?')
        ->and($query->getBindings())->toContain($userId);
});

test('user list query builder can filter by listable type', function () {
    // Arrange
    $listableType = Movie::class;

    // Act
    $query = UserList::forListableType($listableType);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"listable_type" = ?')
        ->and($query->getBindings())->toContain($listableType);
});

test('user list query builder can filter by listable', function () {
    // Arrange
    $listableType = Movie::class;
    $listableId = 'test-movie-id';

    // Act
    $query = UserList::forListable($listableType, $listableId);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"listable_type" = ?')
        ->and($sql)->toContain('"listable_id" = ?')
        ->and($query->getBindings())->toContain($listableType)
        ->and($query->getBindings())->toContain($listableId);
});

test('user list query builder can get favorites', function () {
    // Act
    $query = UserList::favorites();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(UserListType::FAVORITE->value);
});

test('user list query builder can get watching', function () {
    // Act
    $query = UserList::watching();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(UserListType::WATCHING->value);
});

test('user list query builder can get planned', function () {
    // Act
    $query = UserList::planned();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(UserListType::PLANNED->value);
});

test('user list query builder can get watched', function () {
    // Act
    $query = UserList::watched();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(UserListType::WATCHED->value);
});

test('user list query builder can get stopped', function () {
    // Act
    $query = UserList::stopped();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(UserListType::STOPPED->value);
});

test('user list query builder can get rewatching', function () {
    // Act
    $query = UserList::rewatching();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(UserListType::REWATCHING->value);
});

test('user list factory creates valid model', function () {
    // Act
    $userList = UserList::factory()->make();

    // Assert
    expect($userList)->toBeInstanceOf(UserList::class)
        ->and($userList->type)->toBeInstanceOf(UserListType::class);
});

// Додаємо тест для створення списку користувача в базі даних
test('can create user list in database', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Act
    $userList = UserList::factory()->create([
        'user_id' => $user->id,
        'listable_id' => $movie->id,
        'listable_type' => Movie::class,
        'type' => UserListType::FAVORITE,
    ]);

    // Assert
    expect($userList)->toBeInstanceOf(UserList::class)
        ->and($userList->exists)->toBeTrue()
        ->and($userList->user_id)->toBe($user->id)
        ->and($userList->listable_id)->toBe($movie->id)
        ->and($userList->listable_type)->toBe(Movie::class)
        ->and($userList->type)->toBe(UserListType::FAVORITE)
        // Перевіряємо, що список користувача дійсно збережений в базі даних
        ->and(UserList::where('id', $userList->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('user_lists', [
        'id' => $userList->id,
        'user_id' => $user->id,
        'listable_id' => $movie->id,
        'listable_type' => Movie::class,
        'type' => UserListType::FAVORITE->value,
    ]);
});

// Додаємо тест для перевірки зв'язків
test('user list belongs to user and morphs to listable', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    $userList = UserList::factory()->create([
        'user_id' => $user->id,
        'listable_id' => $movie->id,
        'listable_type' => Movie::class,
        'type' => UserListType::FAVORITE,
    ]);

    // Act & Assert
    expect($userList->user)->toBeInstanceOf(User::class)
        ->and($userList->user->id)->toBe($user->id)
        ->and($userList->listable)->toBeInstanceOf(Movie::class)
        ->and($userList->listable->id)->toBe($movie->id);
});
