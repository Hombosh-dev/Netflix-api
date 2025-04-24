<?php

use App\Models\Builders\StudioQueryBuilder;
use App\Models\Movie;
use App\Models\Studio;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Studio
test('studio has correct query builder', function () {
    expect(Studio::query())->toBeInstanceOf(StudioQueryBuilder::class);
});

test('studio has correct relationships', function () {
    $studio = new Studio();

    expect($studio->movies())->toBeInstanceOf(HasMany::class);
});

test('studio has correct casts', function () {
    $studio = new Studio();
    $casts = $studio->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('aliases')
        ->and($casts['aliases'])->toBe(AsCollection::class);
});

test('studio query builder can filter by name', function () {
    // Arrange
    $name = 'Warner';

    // Act
    $query = Studio::byName($name);
    $sql = $query->toSql();

    // Assert
    // PostgreSQL використовує інший синтаксис для LIKE оператора
    expect($sql)->toContain('name')
        ->toContain('like')
        ->and($query->getBindings())->toContain('%Warner%');
});

test('studio query builder can get studios with movies', function () {
    // Act
    $query = Studio::withMovies();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('"movies"."studio_id"');
});

test('studio query builder can get studios with movie count', function () {
    // Act
    $query = Studio::withMovieCount();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('select')
        ->and($sql)->toContain('count(*)')
        ->and($sql)->toContain('"movies"."studio_id"');
});

test('studio query builder can order by movie count', function () {
    // Arrange
    $direction = 'desc';

    // Act
    $query = Studio::orderByMovieCount($direction);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by "movies_count" desc');
});

test('studio factory creates valid model', function () {
    // Act
    $studio = Studio::factory()->make();

    // Assert
    expect($studio)->toBeInstanceOf(Studio::class)
        ->and($studio->name)->not->toBeEmpty()
        ->and($studio->slug)->not->toBeEmpty()
        ->and($studio->description)->not->toBeEmpty();
});

// Додаємо тест для створення студії в базі даних
test('can create studio in database', function () {
    // Act
    $studio = Studio::factory()->create([
        'name' => 'Warner Bros',
        'slug' => 'warner-bros',
        'description' => 'Famous American film studio',
        'image' => 'images/warner-bros.jpg',
        'aliases' => ['WB', 'Warner Brothers'],
    ]);

    // Assert
    expect($studio)->toBeInstanceOf(Studio::class)
        ->and($studio->exists)->toBeTrue()
        ->and($studio->name)->toBe('Warner Bros')
        ->and($studio->slug)->toBe('warner-bros')
        ->and($studio->description)->toBe('Famous American film studio')
        ->and($studio->image)->toBe('images/warner-bros.jpg')
        ->and($studio->aliases->toArray())->toBe(['WB', 'Warner Brothers'])
        // Перевіряємо, що студія дійсно збережена в базі даних
        ->and(Studio::where('id', $studio->id)->exists())->toBeTrue();



    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('studios', [
        'id' => $studio->id,
        'name' => 'Warner Bros',
        'slug' => 'warner-bros',
        'description' => 'Famous American film studio',
        'image' => 'images/warner-bros.jpg',
    ]);
});

// Додаємо тест для перевірки зв'язку з фільмами
test('can have movies', function () {
    // Arrange
    $studio = Studio::factory()->create();

    // Створюємо фільми по одному, щоб гарантувати їх створення
    $movie1 = Movie::factory()->create(['studio_id' => $studio->id]);
    $movie2 = Movie::factory()->create(['studio_id' => $studio->id]);

    // Act
    $studioMovies = $studio->movies;

    // Assert
    expect($studioMovies)->toHaveCount(2)
        // Перевіряємо, що всі фільми належать до цієї студії
        ->and($movie1->studio_id)->toBe($studio->id)
        ->and($movie2->studio_id)->toBe($studio->id);

    $this->assertDatabaseHas('movies', [
        'id' => $movie1->id,
        'studio_id' => $studio->id
    ]);

    $this->assertDatabaseHas('movies', [
        'id' => $movie2->id,
        'studio_id' => $studio->id
    ]);
});
