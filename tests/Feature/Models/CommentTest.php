<?php

use App\Models\Builders\CommentQueryBuilder;
use App\Models\Comment;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Comment
test('comment has correct query builder', function () {
    expect(Comment::query())->toBeInstanceOf(CommentQueryBuilder::class);
});

test('comment has correct relationships', function () {
    $comment = new Comment();

    // Перевіряємо відношення
    expect($comment->user())->toBeInstanceOf(BelongsTo::class)
        ->and($comment->commentable())->toBeInstanceOf(MorphTo::class)
        ->and($comment->parent())->toBeInstanceOf(BelongsTo::class);

    // Перевіряємо метод replies() окремо
    $replies = $comment->replies();

    // В Pest немає прямого аналога для перевірки "instanceof A || instanceof B"
    // Тому використовуємо ручну перевірку
    $isValidType = $replies instanceof HasMany || $replies instanceof CommentQueryBuilder;
    expect($isValidType)->toBeTrue('replies() має повертати HasMany або CommentQueryBuilder')
        // Перевіряємо інші відношення
        ->and($comment->likes())->toBeInstanceOf(HasMany::class)
        ->and($comment->reports())->toBeInstanceOf(HasMany::class);


});

test('comment has correct casts', function () {
    $comment = new Comment();
    $casts = $comment->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('is_spoiler')
        ->and($casts['is_spoiler'])->toBe('boolean');
});

test('comment query builder can get replies', function () {
    // Arrange
    $query = Comment::replies();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"parent_id" is not null');
});

test('comment query builder can get root comments', function () {
    // Arrange
    $query = Comment::roots();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"parent_id" is null');
});

test('comment query builder can filter by user', function () {
    // Arrange
    $userId = 'test-user-id';

    // Act
    $query = Comment::forUser($userId);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"user_id" = ?')
        ->and($query->getBindings())->toContain($userId);
});

test('comment query builder can filter by commentable', function () {
    // Arrange
    $commentableType = 'App\\Models\\Movie';
    $commentableId = 'test-movie-id';

    // Act
    $query = Comment::forCommentable($commentableType, $commentableId);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"commentable_type" = ?')
        ->and($sql)->toContain('"commentable_id" = ?')
        ->and($query->getBindings())->toContain($commentableType)
        ->and($query->getBindings())->toContain($commentableId);
});

test('comment query builder can filter spoilers', function () {
    // Перевіряємо фільтр для коментарів зі спойлерами
    $query = Comment::withSpoilers();
    $sql = $query->toSql();

    expect($sql)->toContain('"is_spoiler" = ?')
        ->and($query->getBindings())->toContain(true);

    // Перевіряємо фільтр для коментарів без спойлерів
    $query = Comment::withoutSpoilers();
    $sql = $query->toSql();

    expect($sql)->toContain('"is_spoiler" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('comment query builder can get most liked comments', function () {
    // Arrange
    $limit = 5;

    // Act
    $query = Comment::mostLiked($limit);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by "likes_count" desc')
        ->and($sql)->toContain("limit {$limit}");
});

test('comment factory creates valid model', function () {
    // Act
    $comment = Comment::factory()->make();

    // Assert
    expect($comment)->toBeInstanceOf(Comment::class)
        ->and($comment->body)->not->toBeEmpty();
});

test('can create comment in database', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Act
    $comment = Comment::factory()
        ->for($user)
        ->for($movie, 'commentable')
        ->create([
            'body' => 'Test comment body',
            'is_spoiler' => true
        ]);

    // Assert
    expect($comment)->toBeInstanceOf(Comment::class)
        ->and($comment->exists)->toBeTrue()
        ->and($comment->body)->toBe('Test comment body')
        ->and($comment->is_spoiler)->toBeTrue()
        ->and($comment->user_id)->toBe($user->id)
        ->and($comment->commentable_id)->toBe($movie->id)
        // Перевіряємо тип коментаря - в базі даних він може зберігатися в різних форматах
        ->and($comment->commentable_type)->toBeIn([
            Movie::class,
            'movie',
            'App\\Models\\Movie'
        ])
        // Перевіряємо, що коментар дійсно збережено в базі даних
        ->and(Comment::where('id', $comment->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Test comment body',
        'is_spoiler' => true,
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
    ]);
});
