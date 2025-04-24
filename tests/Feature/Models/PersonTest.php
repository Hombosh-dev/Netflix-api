<?php

use App\Enums\Gender;
use App\Enums\PersonType;
use App\Models\Builders\PersonQueryBuilder;
use App\Models\Movie;
use App\Models\Person;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Person
test('person has correct query builder', function () {
    expect(Person::query())->toBeInstanceOf(PersonQueryBuilder::class);
});

test('person has correct relationships', function () {
    $person = new Person();

    expect($person->movies())->toBeInstanceOf(BelongsToMany::class)
        ->and($person->userLists())->toBeInstanceOf(MorphMany::class)
        ->and($person->selections())->toBeInstanceOf(MorphToMany::class);
});

test('person has correct casts', function () {
    $person = new Person();
    $casts = $person->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('type')
        ->toHaveKey('gender')
        ->toHaveKey('birthday')
        ->and($casts['type'])->toBe(PersonType::class)
        ->and($casts['gender'])->toBe(Gender::class)
        ->and($casts['birthday'])->toBe('date');
});

test('person query builder can filter by type', function () {
    // Arrange
    $type = PersonType::ACTOR;

    // Act
    $query = Person::byType($type);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain($type->value);
});

test('person query builder can filter by name', function () {
    // Arrange
    $name = 'Tom';

    // Act
    $query = Person::byName($name);
    $sql = $query->toSql();

    // Assert
    // PostgreSQL використовує інший синтаксис для LIKE оператора
    expect($sql)->toContain('name')
        ->toContain('like')
        ->and($query->getBindings())->toContain('%Tom%');
});

test('person query builder can filter by gender', function () {
    // Arrange
    $gender = Gender::MALE;

    // Act
    $query = Person::byGender($gender);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"gender" = ?')
        ->and($query->getBindings())->toContain($gender->value);
});

test('person query builder can get actors', function () {
    // Act
    $query = Person::actors();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(PersonType::ACTOR->value);
});

test('person query builder can get directors', function () {
    // Act
    $query = Person::directors();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(PersonType::DIRECTOR->value);
});

test('person query builder can get writers', function () {
    // Act
    $query = Person::writers();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"type" = ?')
        ->and($query->getBindings())->toContain(PersonType::WRITER->value);
});

test('person query builder can get persons with movies', function () {
    // Act
    $query = Person::withMovies();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('"movie_person"."person_id"');
});

test('person query builder can get persons with movie count', function () {
    // Act
    $query = Person::withMovieCount();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('select')
        ->and($sql)->toContain('count(*)')
        ->and($sql)->toContain('"movie_person"."person_id"');
});

test('person query builder can order by movie count', function () {
    // Arrange
    $direction = 'desc';

    // Act
    $query = Person::orderByMovieCount($direction);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by "movies_count" desc');
});

test('person factory creates valid model', function () {
    // Act
    $person = Person::factory()->make();

    // Assert
    expect($person)->toBeInstanceOf(Person::class)
        ->and($person->name)->not->toBeEmpty()
        ->and($person->slug)->not->toBeEmpty()
        ->and($person->type)->toBeInstanceOf(PersonType::class);
});

// Додаємо тест для створення персони в базі даних
test('can create person in database', function () {
    // Act
    $person = Person::factory()->create([
        'name' => 'Tom Hanks',
        'slug' => 'tom-hanks',
        'type' => PersonType::ACTOR,
        'gender' => Gender::MALE,
        'birthday' => '1956-07-09',
        'description' => 'Famous American actor',
        'birthplace' => 'Concord, California, USA',
        'image' => 'photos/tom-hanks.jpg',
    ]);

    // Assert
    expect($person)->toBeInstanceOf(Person::class)
        ->and($person->exists)->toBeTrue()
        ->and($person->name)->toBe('Tom Hanks')
        ->and($person->slug)->toBe('tom-hanks')
        ->and($person->type)->toBe(PersonType::ACTOR)
        ->and($person->gender)->toBe(Gender::MALE)
        ->and($person->birthday->format('Y-m-d'))->toBe('1956-07-09')
        ->and($person->description)->toBe('Famous American actor')
        ->and($person->birthplace)->toBe('Concord, California, USA')
        ->and($person->image)->toBe('photos/tom-hanks.jpg')
        ->and(Person::where('id', $person->id)->exists())->toBeTrue();    // Перевіряємо, що персона дійсно збережена в базі даних

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Tom Hanks',
        'slug' => 'tom-hanks',
        'type' => PersonType::ACTOR->value,
        'gender' => Gender::MALE->value,
        'description' => 'Famous American actor',
        'birthplace' => 'Concord, California, USA',
        'image' => 'photos/tom-hanks.jpg',
    ]);
});

// Додаємо тест для перевірки зв'язку з фільмами
test('can attach movies to person', function () {
    // Arrange
    $person = Person::factory()->create([
        'name' => 'Tom Hanks',
        'type' => PersonType::ACTOR,
    ]);

    $movies = Movie::factory()->count(2)->create();

    // Act - додаємо фільми до персони
    foreach ($movies as $index => $movie) {
        $person->movies()->attach($movie->id, [
            'character_name' => "Character " . ($index + 1)
        ]);
    }

    // Assert
    expect($person->movies)->toHaveCount(2);

    // Перевіряємо, що зв'язки збережені в проміжній таблиці
    foreach ($movies as $index => $movie) {
        $this->assertDatabaseHas('movie_person', [
            'movie_id' => $movie->id,
            'person_id' => $person->id,
            'character_name' => "Character " . ($index + 1)
        ]);
    }
});
