<?php

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі CommentLike
test('comment like has correct relationships', function () {
    $commentLike = new CommentLike();

    expect($commentLike->user())->toBeInstanceOf(BelongsTo::class)
        ->and($commentLike->comment())->toBeInstanceOf(BelongsTo::class);
});

test('comment like has correct casts', function () {
    $commentLike = new CommentLike();
    $casts = $commentLike->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('is_liked')
        ->and($casts['is_liked'])->toBe('boolean');
});

test('comment like factory creates valid model', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $commentLike = CommentLike::factory()->make([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true,
    ]);

    // Assert
    expect($commentLike)->toBeInstanceOf(CommentLike::class)
        ->and($commentLike->user_id)->toBe($user->id)
        ->and($commentLike->comment_id)->toBe($comment->id)
        ->and($commentLike->is_liked)->toBeTrue();
});

// Додаємо тест для створення лайку коментаря в базі даних
test('can create comment like in database', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $commentLike = CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true,
    ]);

    // Assert
    expect($commentLike)->toBeInstanceOf(CommentLike::class)
        ->and($commentLike->exists)->toBeTrue()
        ->and($commentLike->user_id)->toBe($user->id)
        ->and($commentLike->comment_id)->toBe($comment->id)
        ->and($commentLike->is_liked)->toBeTrue()
        // Перевіряємо, що лайк коментаря дійсно збережений в базі даних
        ->and(CommentLike::where('id', $commentLike->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('comment_likes', [
        'id' => $commentLike->id,
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true,
    ]);
});

// Додаємо тест для перевірки унікальності лайку коментаря
test('comment like is unique for user and comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act - створюємо перший лайк
    $commentLike = CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true,
    ]);

    // Assert - перевіряємо, що другий лайк викликає помилку
    $this->expectException(\Illuminate\Database\QueryException::class);

    CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => false, // Навіть якщо значення is_liked інше
    ]);
});

// Додаємо тест для перевірки зв'язків з іншими моделями
test('comment like belongs to user and comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $commentLike = CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true,
    ]);

    // Act & Assert
    expect($commentLike->user)->toBeInstanceOf(User::class)
        ->and($commentLike->user->id)->toBe($user->id)
        ->and($commentLike->comment)->toBeInstanceOf(Comment::class)
        ->and($commentLike->comment->id)->toBe($comment->id);
});

// Додаємо тест для перевірки каскадного видалення
test('comment like is deleted when comment is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $commentLike = CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true,
    ]);

    // Act - видаляємо коментар
    $comment->delete();

    // Assert - перевіряємо, що лайк також видалено
    expect(CommentLike::where('id', $commentLike->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('comment_likes', [
        'id' => $commentLike->id,
    ]);
});

// Додаємо тест для перевірки каскадного видалення при видаленні користувача
test('comment like is deleted when user is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $commentLike = CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true,
    ]);

    // Act - видаляємо користувача
    $user->delete();

    // Assert - перевіряємо, що лайк також видалено
    expect(CommentLike::where('id', $commentLike->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('comment_likes', [
        'id' => $commentLike->id,
    ]);
});
