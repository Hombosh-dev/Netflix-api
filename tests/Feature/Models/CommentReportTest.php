<?php

use App\Enums\CommentReportType;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі CommentReport
test('comment report has correct relationships', function () {
    $commentReport = new CommentReport();

    expect($commentReport->user())->toBeInstanceOf(BelongsTo::class)
        ->and($commentReport->comment())->toBeInstanceOf(BelongsTo::class);
});

test('comment report has correct casts', function () {
    $commentReport = new CommentReport();
    $casts = $commentReport->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKeys(['type', 'is_viewed'])
        ->and($casts['type'])->toBe(CommentReportType::class)
        ->and($casts['is_viewed'])->toBe('boolean');
});

test('comment report factory creates valid model', function () {
    // Arrange
    $user = User::factory()->create();
    $reporter = User::factory()->create(); // Користувач, який створює скаргу
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $commentReport = CommentReport::factory()->make([
        'user_id' => $reporter->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::AD_SPAM,
        'is_viewed' => false,
        'body' => 'This comment is spam',
    ]);

    // Assert
    expect($commentReport)->toBeInstanceOf(CommentReport::class)
        ->and($commentReport->user_id)->toBe($reporter->id)
        ->and($commentReport->comment_id)->toBe($comment->id)
        ->and($commentReport->type)->toBe(CommentReportType::AD_SPAM)
        ->and($commentReport->is_viewed)->toBeFalse()
        ->and($commentReport->body)->toBe('This comment is spam');
});

// Додаємо тест для створення скарги на коментар в базі даних
test('can create comment report in database', function () {
    // Arrange
    $user = User::factory()->create();
    $reporter = User::factory()->create(); // Користувач, який створює скаргу
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $commentReport = CommentReport::factory()->create([
        'user_id' => $reporter->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::AD_SPAM,
        'is_viewed' => false,
        'body' => 'This comment is spam',
    ]);

    // Assert
    expect($commentReport)->toBeInstanceOf(CommentReport::class)
        ->and($commentReport->exists)->toBeTrue()
        ->and($commentReport->user_id)->toBe($reporter->id)
        ->and($commentReport->comment_id)->toBe($comment->id)
        ->and($commentReport->type)->toBe(CommentReportType::AD_SPAM)
        ->and($commentReport->is_viewed)->toBeFalse()
        ->and($commentReport->body)->toBe('This comment is spam')
        // Перевіряємо, що скарга на коментар дійсно збережена в базі даних
        ->and(CommentReport::where('id', $commentReport->id)->exists())->toBeTrue();
    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport->id,
        'user_id' => $reporter->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::AD_SPAM->value,
        'is_viewed' => false,
        'body' => 'This comment is spam',
    ]);
});

// Додаємо тест для перевірки створення кількох скарг на один коментар
test('can create multiple reports for one comment from different users', function () {
    // Arrange
    $user = User::factory()->create();
    $reporter1 = User::factory()->create(); // Перший користувач, який створює скаргу
    $reporter2 = User::factory()->create(); // Другий користувач, який створює скаргу
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act - створюємо дві скарги від різних користувачів
    $commentReport1 = CommentReport::factory()->create([
        'user_id' => $reporter1->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::AD_SPAM,
        'is_viewed' => false,
        'body' => 'This comment is spam',
    ]);

    $commentReport2 = CommentReport::factory()->create([
        'user_id' => $reporter2->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT,
        'is_viewed' => false,
        'body' => 'This comment is offensive',
    ]);

    // Assert - перевіряємо, що обидві скарги створено
    expect(CommentReport::count())->toBe(2);

    // Перевіряємо, що обидві скарги збережено в базі даних
    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport1->id,
        'user_id' => $reporter1->id,
        'comment_id' => $comment->id,
    ]);

    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport2->id,
        'user_id' => $reporter2->id,
        'comment_id' => $comment->id,
    ]);
});

// Додаємо тест для перевірки зв'язків з іншими моделями
test('comment report belongs to user and comment', function () {
    // Arrange
    $reporter = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $commentReport = CommentReport::factory()->create([
        'user_id' => $reporter->id,
        'comment_id' => $comment->id,
    ]);

    // Assert - перевіряємо зв'язки
    expect($commentReport->refresh()->user)->toBeInstanceOf(User::class)
        ->and($commentReport->user->id)->toBe($reporter->id)
        ->and($commentReport->comment)->toBeInstanceOf(Comment::class)
        ->and($commentReport->comment->id)->toBe($comment->id);
});

// Додаємо тест для перевірки каскадного видалення
test('comment report is deleted when comment is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $reporter = User::factory()->create(); // Користувач, який створює скаргу
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $commentReport = CommentReport::factory()->create([
        'user_id' => $reporter->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::AD_SPAM,
        'is_viewed' => false,
        'body' => 'This comment is spam',
    ]);

    // Act - видаляємо коментар
    $comment->delete();

    // Assert - перевіряємо, що скарга також видалена
    expect(CommentReport::where('id', $commentReport->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('comment_reports', [
        'id' => $commentReport->id,
    ]);
});

// Додаємо тест для перевірки каскадного видалення при видаленні користувача
test('comment report is deleted when user is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $reporter = User::factory()->create(); // Користувач, який створює скаргу
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $commentReport = CommentReport::factory()->create([
        'user_id' => $reporter->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::AD_SPAM,
        'is_viewed' => false,
        'body' => 'This comment is spam',
    ]);

    // Act - видаляємо користувача, який створив скаргу
    $reporter->delete();

    // Assert - перевіряємо, що скарга також видалена
    expect(CommentReport::where('id', $commentReport->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('comment_reports', [
        'id' => $commentReport->id,
    ]);
});

// Додаємо тест для перевірки оновлення статусу перегляду
test('can update comment report viewed status', function () {
    // Arrange
    $user = User::factory()->create();
    $reporter = User::factory()->create(); // Користувач, який створює скаргу
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $commentReport = CommentReport::factory()->create([
        'user_id' => $reporter->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::AD_SPAM,
        'is_viewed' => false,
        'body' => 'This comment is spam',
    ]);

    // Act - оновлюємо статус перегляду
    $commentReport->is_viewed = true;
    $commentReport->save();

    // Оновлюємо модель з бази даних
    $commentReport->refresh();

    // Assert - перевіряємо, що статус перегляду оновлено
    expect($commentReport->is_viewed)->toBeTrue();

    // Перевіряємо, що зміни збережені в базі даних
    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport->id,
        'is_viewed' => true,
    ]);
});
