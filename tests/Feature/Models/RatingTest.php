<?php

use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Rating
test('rating has correct relationships', function () {
    $rating = new Rating();

    expect($rating->user())->toBeInstanceOf(BelongsTo::class)
        ->and($rating->movie())->toBeInstanceOf(BelongsTo::class);
});

test('rating factory creates valid model', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    // Act
    $rating = Rating::factory()->make([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
        'review' => 'This is a great movie!',
    ]);
    
    // Assert
    expect($rating)->toBeInstanceOf(Rating::class)
        ->and($rating->user_id)->toBe($user->id)
        ->and($rating->movie_id)->toBe($movie->id)
        ->and($rating->number)->toBe(8)
        ->and($rating->review)->toBe('This is a great movie!');
});

// Додаємо тест для створення рейтингу в базі даних
test('can create rating in database', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    // Act
    $rating = Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
        'review' => 'This is a great movie!',
    ]);
    
    // Assert
    expect($rating)->toBeInstanceOf(Rating::class)
        ->and($rating->exists)->toBeTrue()
        ->and($rating->user_id)->toBe($user->id)
        ->and($rating->movie_id)->toBe($movie->id)
        ->and($rating->number)->toBe(8)
        ->and($rating->review)->toBe('This is a great movie!');
    
    // Перевіряємо, що рейтинг дійсно збережений в базі даних
    expect(Rating::where('id', $rating->id)->exists())->toBeTrue();
    
    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('ratings', [
        'id' => $rating->id,
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
        'review' => 'This is a great movie!',
    ]);
});

// Додаємо тест для перевірки унікальності рейтингу для користувача та фільму
test('rating is unique for user and movie', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    // Act - створюємо перший рейтинг
    Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
        'review' => 'This is a great movie!',
    ]);
    
    // Assert - перевіряємо, що другий рейтинг викликає помилку
    $this->expectException(\Illuminate\Database\QueryException::class);
    
    Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 9, // Навіть якщо оцінка інша
        'review' => 'Changed my mind, it is even better!', // Навіть якщо відгук інший
    ]);
});

// Додаємо тест для перевірки обмеження на значення number
test('rating number must be between 1 and 10', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    // Assert - перевіряємо, що рейтинг з number = 0 викликає помилку
    $this->expectException(\Illuminate\Database\QueryException::class);
    
    Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 0, // Менше мінімального значення
        'review' => 'This is a terrible movie!',
    ]);
    
    // Assert - перевіряємо, що рейтинг з number = 11 викликає помилку
    $this->expectException(\Illuminate\Database\QueryException::class);
    
    Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 11, // Більше максимального значення
        'review' => 'This is an amazing movie!',
    ]);
});

// Додаємо тест для перевірки зв'язків з іншими моделями
test('rating belongs to user and movie', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    $rating = Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
        'review' => 'This is a great movie!',
    ]);
    
    // Act - завантажуємо зв'язки
    $rating->load(['user', 'movie']);
    
    // Assert - перевіряємо зв'язки
    expect($rating->user)->toBeInstanceOf(User::class)
        ->and($rating->user->id)->toBe($user->id)
        ->and($rating->movie)->toBeInstanceOf(Movie::class)
        ->and($rating->movie->id)->toBe($movie->id);
});

// Додаємо тест для перевірки каскадного видалення при видаленні користувача
test('rating is deleted when user is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    $rating = Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
        'review' => 'This is a great movie!',
    ]);
    
    // Act - видаляємо користувача
    $user->delete();
    
    // Assert - перевіряємо, що рейтинг також видалено
    expect(Rating::where('id', $rating->id)->exists())->toBeFalse();
    
    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('ratings', [
        'id' => $rating->id,
    ]);
});

// Додаємо тест для перевірки каскадного видалення при видаленні фільму
test('rating is deleted when movie is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    $rating = Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
        'review' => 'This is a great movie!',
    ]);
    
    // Act - видаляємо фільм
    $movie->delete();
    
    // Assert - перевіряємо, що рейтинг також видалено
    expect(Rating::where('id', $rating->id)->exists())->toBeFalse();
    
    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('ratings', [
        'id' => $rating->id,
    ]);
});

// Додаємо тест для перевірки оновлення рейтингу
test('can update rating', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    $rating = Rating::factory()->create([
        'user_id' => $user->id,
        'movie_id' => $movie->id,
        'number' => 8,
        'review' => 'This is a great movie!',
    ]);
    
    // Act - оновлюємо рейтинг
    $rating->number = 9;
    $rating->review = 'This is an amazing movie!';
    $rating->save();
    
    // Оновлюємо модель з бази даних
    $rating->refresh();
    
    // Assert - перевіряємо, що рейтинг оновлено
    expect($rating->number)->toBe(9)
        ->and($rating->review)->toBe('This is an amazing movie!');
    
    // Перевіряємо, що зміни збережені в базі даних
    $this->assertDatabaseHas('ratings', [
        'id' => $rating->id,
        'number' => 9,
        'review' => 'This is an amazing movie!',
    ]);
});
