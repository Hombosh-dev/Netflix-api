<?php

use App\Models\Builders\SelectionQueryBuilder;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Selection
test('selection has correct query builder', function () {
    expect(Selection::query())->toBeInstanceOf(SelectionQueryBuilder::class);
});

test('selection has correct relationships', function () {
    $selection = new Selection();

    expect($selection->user())->toBeInstanceOf(BelongsTo::class)
        ->and($selection->movies())->toBeInstanceOf(MorphToMany::class)
        ->and($selection->persons())->toBeInstanceOf(MorphToMany::class)
        ->and($selection->userLists())->toBeInstanceOf(MorphMany::class)
        ->and($selection->comments())->toBeInstanceOf(MorphMany::class);
});

test('selection has correct casts', function () {
    $selection = new Selection();
    $casts = $selection->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('is_published')
        ->and($casts['is_published'])->toBe('boolean');
});

test('selection query builder can filter published selections', function () {
    // Arrange
    $query = Selection::published();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_published" = ?')
        ->and($query->getBindings())->toContain(true);
});

test('selection query builder can filter by user', function () {
    // Arrange
    $userId = 'test-user-id';

    // Act
    $query = Selection::byUser($userId);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"user_id" = ?')
        ->and($query->getBindings())->toContain($userId);
});

test('selection query builder can filter with movies', function () {
    // Arrange
    $query = Selection::withMovies();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('movies');
});

test('selection query builder can filter with persons', function () {
    // Arrange
    $query = Selection::withPersons();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('people');
});

test('selection factory creates valid model', function () {
    // Act
    $selection = Selection::factory()->make();

    // Assert
    expect($selection)->toBeInstanceOf(Selection::class)
        ->and($selection->name)->not->toBeEmpty()
        ->and($selection->slug)->not->toBeEmpty()
        ->and($selection->description)->not->toBeEmpty();
});

test('can create selection in database', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $selection = Selection::factory()
        ->create([
            'user_id' => $user->id,
            'name' => 'Test Selection',
            'slug' => 'test-selection',
            'description' => 'Test selection description',
            'is_published' => true,
        ]);

    // Assert
    expect($selection)->toBeInstanceOf(Selection::class)
        ->and($selection->exists)->toBeTrue()
        ->and($selection->name)->toBe('Test Selection')
        ->and($selection->slug)->toBe('test-selection')
        ->and($selection->description)->toBe('Test selection description')
        ->and($selection->is_published)->toBeTrue()
        ->and($selection->user_id)->toBe($user->id)
        // Перевіряємо, що підбірка дійсно збережена в базі даних
        ->and(Selection::where('id', $selection->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('selections', [
        'id' => $selection->id,
        'name' => 'Test Selection',
        'slug' => 'test-selection',
        'description' => 'Test selection description',
        'user_id' => $user->id,
    ]);
});

test('can add movies to selection', function () {
    // Arrange
    $user = User::factory()->create();
    $selection = Selection::factory()->create([
        'user_id' => $user->id,
    ]);
    $movies = Movie::factory(3)->create();

    // Act - використовуємо прямий SQL запит для додавання зв'язків
    foreach ($movies as $movie) {
        DB::table('selectionables')->insert([
            'selection_id' => $selection->id,
            'selectionable_id' => $movie->id,
            'selectionable_type' => Movie::class,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Оновлюємо модель з бази даних
    $selection->refresh();

    // Assert
    expect($selection->movies)->toHaveCount(3)
        ->and($selection->movies->pluck('id')->toArray())->toEqual($movies->pluck('id')->toArray());

    // Перевіряємо, що зв'язки збережені в базі даних
    foreach ($movies as $movie) {
        $this->assertDatabaseHas('selectionables', [
            'selection_id' => $selection->id,
            'selectionable_id' => $movie->id,
            'selectionable_type' => Movie::class,
        ]);
    }
});

test('can add persons to selection', function () {
    // Arrange
    $user = User::factory()->create();
    $selection = Selection::factory()->create([
        'user_id' => $user->id,
    ]);
    $persons = Person::factory(3)->create();

    // Act - використовуємо прямий SQL запит для додавання зв'язків
    foreach ($persons as $person) {
        DB::table('selectionables')->insert([
            'selection_id' => $selection->id,
            'selectionable_id' => $person->id,
            'selectionable_type' => Person::class,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Оновлюємо модель з бази даних
    $selection->refresh();

    // Assert
    expect($selection->persons)->toHaveCount(3)
        ->and($selection->persons->pluck('id')->toArray())->toEqual($persons->pluck('id')->toArray());

    // Перевіряємо, що зв'язки збережені в базі даних
    foreach ($persons as $person) {
        $this->assertDatabaseHas('selectionables', [
            'selection_id' => $selection->id,
            'selectionable_id' => $person->id,
            'selectionable_type' => Person::class,
        ]);
    }
});

test('selection belongs to user', function () {
    // Arrange
    $user = User::factory()->create();
    $selection = Selection::factory()->create([
        'user_id' => $user->id,
    ]);

    // Act - завантажуємо зв'язок
    $selection->load('user');

    // Assert - перевіряємо зв'язок
    expect($selection->user)->toBeInstanceOf(User::class)
        ->and($selection->user->id)->toBe($user->id);
});

test('selection is deleted when user is deleted', function () {
    // Arrange
    $user = User::factory()->create();
    $selection = Selection::factory()->create([
        'user_id' => $user->id,
    ]);

    // Act - видаляємо користувача
    $user->delete();

    // Assert - перевіряємо, що підбірка також видалена
    expect(Selection::where('id', $selection->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('selections', [
        'id' => $selection->id,
    ]);
});

test('can remove movies from selection', function () {
    // Arrange
    $user = User::factory()->create();
    $selection = Selection::factory()->create([
        'user_id' => $user->id,
    ]);
    $movies = Movie::factory(3)->create();
    $selection->movies()->attach($movies->pluck('id')->toArray());

    // Act - видаляємо один фільм з підбірки
    $selection->movies()->detach($movies->first()->id);

    // Оновлюємо модель з бази даних
    $selection->refresh();

    // Assert
    expect($selection->movies->pluck('id')->toArray())->not->toContain($movies->first()->id);

    // Перевіряємо, що зв'язок видалено з бази даних
    $this->assertDatabaseMissing('selectionables', [
        'selection_id' => $selection->id,
        'selectionable_id' => $movies->first()->id,
        'selectionable_type' => Movie::class,
    ]);
});

test('can update selection', function () {
    // Arrange
    $user = User::factory()->create();
    $selection = Selection::factory()->create([
        'user_id' => $user->id,
        'name' => 'Original Name',
        'description' => 'Original description',
        'is_published' => false,
    ]);

    // Act - оновлюємо підбірку
    $selection->name = 'Updated Name';
    $selection->description = 'Updated description';
    $selection->is_published = true;
    $selection->save();

    // Оновлюємо модель з бази даних
    $selection->refresh();

    // Assert
    expect($selection->name)->toBe('Updated Name')
        ->and($selection->description)->toBe('Updated description')
        ->and($selection->is_published)->toBeTrue();

    // Перевіряємо, що зміни збережені в базі даних
    $this->assertDatabaseHas('selections', [
        'id' => $selection->id,
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'is_published' => true,
    ]);
});

test('selection query builder can order by movie count', function () {
    // Arrange
    $query = Selection::orderByMovieCount('desc');

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by "movies_count" desc');
});

test('selection query builder can filter with comments', function () {
    // Arrange
    $query = Selection::withComments();

    // Act
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('comments');
});

test('selection uses HasSeo trait', function () {
    // Arrange
    $name = 'Test Selection';

    // Act
    $slug = Selection::generateSlug($name);

    // Assert
    expect($slug)->toStartWith('test-selection-')
        ->and(strlen($slug))->toBeGreaterThan(strlen('test-selection-'));
});

test('selection uses HasSearchable trait', function () {
    // Arrange
    $user = User::factory()->create();
    Selection::factory()->create([
        'user_id' => $user->id,
        'name' => 'Unique Test Selection',
        'description' => 'This is a unique description for testing search',
    ]);

    // Act
    $results = Selection::search('unique test')->get();

    // Assert
    expect($results)->toHaveCount(1)
        ->and($results->first()->name)->toBe('Unique Test Selection');
});
