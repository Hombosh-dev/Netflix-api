<?php

namespace Tests\Feature\Models\Traits;

use App\Enums\UserListType;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\Traits\HasUserInteractions;
use App\Models\User;
use App\Models\UserList;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Тест для перевірки методу userLists()
test('has user interactions trait provides userLists method', function () {
    // Arrange - створюємо користувача, фільм та запис у списку
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    $userList = UserList::factory()->create([
        'user_id' => $user->id,
        'listable_id' => $movie->id,
        'listable_type' => Movie::class,
        'type' => UserListType::FAVORITE
    ]);

    // Act - перевіряємо наявність методу userLists
    $userListsRelation = $movie->userLists();

    // Assert - перевіряємо, що метод повертає правильний тип зв'язку
    expect($userListsRelation)->toBeInstanceOf(MorphMany::class);

    // Перевіряємо, що запис існує в базі даних
    $this->assertDatabaseHas('user_lists', [
        'user_id' => $user->id,
        'listable_id' => $movie->id,
        'listable_type' => Movie::class,
        'type' => UserListType::FAVORITE->value
    ]);
});

// Тест для перевірки методу comments()
test('has user interactions trait provides comments method', function () {
    // Arrange - створюємо користувача, фільм та коментар
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'Test comment'
    ]);

    // Act - перевіряємо наявність методу comments
    $commentsRelation = $movie->comments();

    // Assert - перевіряємо, що метод повертає правильний тип зв'язку
    expect($commentsRelation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\MorphMany::class);

    // Перевіряємо, що запис існує в базі даних
    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'Test comment'
    ]);
});

// Тест для перевірки зв'язку між моделями
test('movie model uses has user interactions trait', function () {
    // Перевіряємо, що модель Movie використовує трейт HasUserInteractions
    $traits = class_uses_recursive(Movie::class);

    expect($traits)->toContain(HasUserInteractions::class);

    // Перевіряємо наявність методів з трейту
    $movie = new Movie();

    // Перевіряємо, що методи існують
    expect(method_exists($movie, 'userLists'))->toBeTrue()
        ->and(method_exists($movie, 'comments'))->toBeTrue();
});
