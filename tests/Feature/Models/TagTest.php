<?php

use App\Models\Builders\TagQueryBuilder;
use App\Models\Movie;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Tag
test('tag has correct query builder', function () {
    expect(Tag::query())->toBeInstanceOf(TagQueryBuilder::class);
});

test('tag has correct relationships', function () {
    $tag = new Tag();

    expect($tag->movies())->toBeInstanceOf(BelongsToMany::class)
        ->and($tag->userLists())->toBeInstanceOf(MorphMany::class);
});

test('tag has correct casts', function () {
    $tag = new Tag();
    $casts = $tag->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('aliases')
        ->toHaveKey('is_genre')
        ->and($casts['aliases'])->toBe(AsCollection::class)
        ->and($casts['is_genre'])->toBe('boolean');
});

test('tag has correct accessors', function () {
    $tag = new Tag([
        'name' => 'Test Tag',
        'image' => 'tags/test.jpg',
    ]);

    expect($tag->image)->toContain('storage/tags/test.jpg');
});

test('tag query builder can filter genres', function () {
    // Act
    $query = Tag::genres();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_genre" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('tag query builder can filter non-genres', function () {
    // Act
    $query = Tag::nonGenres();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_genre" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('tag query builder can search tags', function () {
    // Arrange
    $searchTerm = 'action';

    // Act
    $query = Tag::search($searchTerm);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('select *')
        ->and($sql)->toContain('ts_rank(searchable, websearch_to_tsquery')
        ->and($sql)->toContain('ts_headline')
        ->and($sql)->toContain('similarity');
});

test('tag query builder can order by popularity', function () {
    // Act
    $query = Tag::popular();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by "movies_count" desc');
});

test('tag factory creates valid model', function () {
    // Act
    $tag = Tag::factory()->make();

    // Assert
    expect($tag)->toBeInstanceOf(Tag::class)
        ->and($tag->name)->not->toBeEmpty()
        ->and($tag->slug)->not->toBeEmpty()
        ->and($tag->description)->not->toBeEmpty();
});

// Додаємо тест для створення тегу в базі даних
test('can create tag in database', function () {
    // Act
    $tag = Tag::factory()->create([
        'name' => 'Action',
        'slug' => 'action',
        'description' => 'Action movies and series',
        'image' => 'tags/action.jpg',
        'aliases' => ['Action Movies', 'Action Films'],
        'is_genre' => true,
    ]);

    // Assert
    expect($tag)->toBeInstanceOf(Tag::class)
        ->and($tag->exists)->toBeTrue()
        ->and($tag->name)->toBe('Action')
        ->and($tag->slug)->toBe('action')
        ->and($tag->description)->toBe('Action movies and series')
        ->and($tag->image)->toContain('storage/tags/action.jpg')
        ->and($tag->aliases->toArray())->toBe(['Action Movies', 'Action Films'])
        ->and($tag->is_genre)->toBeTrue()
        // Перевіряємо, що тег дійсно збережений в базі даних
        ->and(Tag::where('id', $tag->id)->exists())->toBeTrue();


    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'name' => 'Action',
        'slug' => 'action',
        'description' => 'Action movies and series',
        'image' => 'tags/action.jpg',
        'is_genre' => true,
    ]);
});

// Додаємо тест для перевірки зв'язку з фільмами
test('can attach movies to tag', function () {
    // Arrange
    $tag = Tag::factory()->create([
        'name' => 'Action',
        'is_genre' => true,
    ]);

    // Створюємо фільми по одному, щоб гарантувати їх створення
    $movie1 = Movie::factory()->create();
    $movie2 = Movie::factory()->create();

    // Act - додаємо фільми до тегу
    $tag->movies()->attach([$movie1->id, $movie2->id]);

    // Оновлюємо модель, щоб завантажити зв'язки
    $tag->refresh();

    // Assert
    // Перевіряємо кількість записів в проміжній таблиці замість кількості об'єктів в колекції
    $count = DB::table('movie_tag')
        ->where('tag_id', $tag->id)
        ->count();

    expect($count)->toBe(2);

    // Перевіряємо, що зв'язки збережені в проміжній таблиці
    $this->assertDatabaseHas('movie_tag', [
        'movie_id' => $movie1->id,
        'tag_id' => $tag->id,
    ]);

    $this->assertDatabaseHas('movie_tag', [
        'movie_id' => $movie2->id,
        'tag_id' => $tag->id,
    ]);
});
