<?php

use App\Enums\Kind;
use App\Enums\Status;
use App\Models\Builders\MovieQueryBuilder;
use App\Models\Movie;
use App\Models\Studio;
use App\Models\Tag;
use App\Models\Person;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Movie
test('movie has correct query builder', function () {
    expect(Movie::query())->toBeInstanceOf(MovieQueryBuilder::class);
});

test('movie has correct relationships', function () {
    $movie = new Movie();

    expect($movie->studio())->toBeInstanceOf(BelongsTo::class)
        ->and($movie->ratings())->toBeInstanceOf(HasMany::class)
        ->and($movie->tags())->toBeInstanceOf(BelongsToMany::class)
        ->and($movie->persons())->toBeInstanceOf(BelongsToMany::class)
        ->and($movie->episodes())->toBeInstanceOf(HasMany::class)
        ->and($movie->userLists())->toBeInstanceOf(MorphMany::class)
        ->and($movie->comments())->toBeInstanceOf(MorphMany::class)
        ->and($movie->selections())->toBeInstanceOf(MorphToMany::class);
});

test('movie has correct casts', function () {
    $movie = new Movie();
    $casts = $movie->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('aliases')
        ->toHaveKey('countries')
        ->toHaveKey('attachments')
        ->toHaveKey('related')
        ->toHaveKey('similars')
        ->toHaveKey('api_sources')
        ->toHaveKey('imdb_score')
        ->toHaveKey('first_air_date')
        ->toHaveKey('last_air_date')
        ->toHaveKey('kind')
        ->toHaveKey('status')
        ->toHaveKey('is_published')
        ->and($casts['kind'])->toBe(Kind::class)
        ->and($casts['status'])->toBe(Status::class)
        ->and($casts['is_published'])->toBe('boolean')
        ->and($casts['imdb_score'])->toBe('float')
        ->and($casts['first_air_date'])->toBe('date')
        ->and($casts['last_air_date'])->toBe('date');
});

test('movie has correct accessors', function () {
    $movie = new Movie([
        'name' => 'Test Movie',
        'first_air_date' => '2023-01-01',
        'duration' => 125,
        'poster' => 'posters/test.jpg',
    ]);

    expect($movie->fullTitle)->toBe('Test Movie (2023)')
        ->and($movie->formattedDuration)->toBe('2 год 5 хв')
        ->and($movie->posterUrl)->toContain('storage/posters/test.jpg')
        ->and($movie->releaseYear)->toBe('2023');
});

test('movie query builder filters by kind', function () {
    // Arrange
    $kind = Kind::MOVIE;

    // Act
    $query = Movie::ofKind($kind);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"kind" = ?')
        ->and($query->getBindings())->toContain($kind->value);
});

test('movie query builder filters by status', function () {
    // Arrange
    $status = Status::RELEASED;

    // Act
    $query = Movie::withStatus($status);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"status" = ?')
        ->and($query->getBindings())->toContain($status->value);
});

test('movie query builder filters by imdb score', function () {
    // Arrange
    $score = 7.5;

    // Act
    $query = Movie::withImdbScoreGreaterThan($score);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"imdb_score" >= ?')
        ->and($query->getBindings())->toContain($score);
});

test('movie query builder can get popular movies', function () {
    // Act
    $query = Movie::popular();
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by "user_lists_count" desc');
});

test('movie query builder can get trending movies', function () {
    // Arrange
    $days = 7;

    // Act
    $query = Movie::trending($days);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by (ratings_count * 2 + comments_count) DESC');
});

test('movie query builder can filter by tags', function () {
    // Arrange
    $tagIds = [1, 2, 3];

    // Act
    $query = Movie::withTags($tagIds);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('"tags"."id"');
});

test('movie query builder can filter by persons', function () {
    // Arrange
    $personIds = [1, 2, 3];

    // Act
    $query = Movie::withPersons($personIds);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('exists')
        ->and($sql)->toContain('"persons"."id"');
});

test('movie query builder can filter by countries', function () {
    // Arrange
    $countries = ['US', 'UK'];

    // Act
    $query = Movie::fromCountries($countries);
    $sql = $query->toSql();

    // Assert
    // PostgreSQL використовує оператор @> для перевірки, чи містить JSON масив певне значення
    // замість JSON_CONTAINS, який використовується в MySQL
    expect($sql)->toContain('countries')
        ->and($sql)->toContain('@>');
});

test('movie factory creates valid model', function () {
    // Act
    $movie = Movie::factory()->make();

    // Assert
    expect($movie)->toBeInstanceOf(Movie::class)
        ->and($movie->name)->not->toBeEmpty()
        ->and($movie->slug)->not->toBeEmpty()
        ->and($movie->description)->not->toBeEmpty();
});

// Додаємо тест для створення фільму в базі даних
test('can create movie in database', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $tags = Tag::factory()->count(3)->create();
    $persons = Person::factory()->count(2)->create();
    $voicePerson = Person::factory()->create();

    // Підготовка даних для складних полів
    $aliases = ['Alternative Title', 'Another Name'];
    $countries = ['US', 'UK', 'UA'];
    $apiSources = [
        [
            'source' => 'imdb',
            'id' => 'tt1234567'
        ],
        [
            'source' => 'tmdb',
            'id' => '987654'
        ]
    ];
    $attachments = [
        [
            'type' => 'trailer',
            'url' => 'https://youtube.com/watch?v=test-trailer',
            'title' => 'Official Trailer',
            'duration' => 120
        ],
        [
            'type' => 'teaser',
            'url' => 'https://youtube.com/watch?v=test-teaser',
            'title' => 'Teaser',
            'duration' => 60
        ]
    ];
    $related = [
        [
            'movie_id' => '01HQ1BFVS0RVQKZP6ZGWG5B1JR', // Умовний ULID
            'type' => 'sequel'
        ]
    ];
    $similars = ['01HQ1BFVS0RVQKZP6ZGWG5B1JS', '01HQ1BFVS0RVQKZP6ZGWG5B1JT']; // Умовні ULID
    $metaTitle = 'Test Movie Name (2023) - Watch Online';
    $metaDescription = 'Watch Test Movie Name online. A thrilling movie about testing.';
    $metaImage = 'seo/test-movie-meta.jpg';

    // Act
    $movie = Movie::factory()
        ->for($studio)
        ->create([
            'name' => 'Test Movie Name',
            'slug' => 'test-movie-name',
            'description' => 'Test movie description',
            'image_name' => 'test-movie-image.jpg',
            'kind' => Kind::MOVIE,
            'status' => Status::RELEASED,
            'is_published' => true,
            'imdb_score' => 8.5,
            'duration' => 120,
            'episodes_count' => null, // Це фільм, а не серіал
            'first_air_date' => '2023-01-15',
            'last_air_date' => null, // Для фільму не потрібно
            'poster' => 'posters/test-movie-poster.jpg',
            'aliases' => $aliases,
            'countries' => $countries,
            'api_sources' => $apiSources,
            'attachments' => $attachments,
            'related' => $related,
            'similars' => $similars,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_image' => $metaImage
        ]);

    // Attach tags and persons
    $movie->tags()->attach($tags);

    // Додаємо персон з вказанням character_name та voice_person_id
    foreach ($persons as $index => $person) {
        $pivotData = [
            'character_name' => "Character " . ($index + 1)
        ];

        // Для першої персони додаємо голосового актора
        if ($index === 0) {
            $pivotData['voice_person_id'] = $voicePerson->id;
        }

        $movie->persons()->attach($person->id, $pivotData);
    }

    // Assert
    expect($movie)->toBeInstanceOf(Movie::class)
        ->and($movie->exists)->toBeTrue()
        ->and($movie->name)->toBe('Test Movie Name')
        ->and($movie->slug)->toBe('test-movie-name')
        ->and($movie->description)->toBe('Test movie description')
        ->and($movie->image_name)->toBe('test-movie-image.jpg')
        ->and($movie->kind)->toBe(Kind::MOVIE)
        ->and($movie->status)->toBe(Status::RELEASED)
        ->and($movie->is_published)->toBeTrue()
        ->and($movie->imdb_score)->toBe(8.5)
        ->and($movie->duration)->toBe(120)
        ->and($movie->episodes_count)->toBeNull()
        ->and($movie->first_air_date->format('Y-m-d'))->toBe('2023-01-15')
        ->and($movie->last_air_date)->toBeNull()
        ->and($movie->poster)->toBe('posters/test-movie-poster.jpg')
        // Перевіряємо колекції та JSON поля
        ->and($movie->aliases->toArray())->toBe($aliases)
        ->and($movie->countries->toArray())->toBe($countries)
        ->and($movie->api_sources->toArray())->toBe($apiSources)
        ->and($movie->attachments->toArray())->toBe($attachments)
        ->and($movie->related->toArray())->toBe($related)
        ->and($movie->similars->toArray())->toBe($similars)
        // Перевіряємо SEO поля
        ->and($movie->meta_title)->toBe($metaTitle)
        ->and($movie->meta_description)->toBe($metaDescription)
        ->and($movie->meta_image)->toBe($metaImage)
        // Перевіряємо зв'язки
        ->and($movie->studio_id)->toBe($studio->id)
        // Перевіряємо зв'язки
        ->and($movie->tags)->toHaveCount(3)
        ->and($movie->persons)->toHaveCount(2)
        // Перевіряємо, що фільм дійсно збережено в базі даних
        ->and(Movie::where('id', $movie->id)->exists())->toBeTrue()
        // Перевіряємо аксесори
        ->and($movie->fullTitle)->toBe('Test Movie Name (2023)')
        ->and($movie->formattedDuration)->toBe('2 год')
        ->and($movie->posterUrl)->toContain('storage/posters/test-movie-poster.jpg')
        ->and($movie->releaseYear)->toBe('2023');


    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('movies', [
        'id' => $movie->id,
        'name' => 'Test Movie Name',
        'slug' => 'test-movie-name',
        'description' => 'Test movie description',
        'image_name' => 'test-movie-image.jpg',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value,
        'is_published' => true,
        'imdb_score' => 8.5,
        'duration' => 120,
        'poster' => 'posters/test-movie-poster.jpg',
        'studio_id' => $studio->id,
        'meta_title' => $metaTitle,
        'meta_description' => $metaDescription,
        'meta_image' => $metaImage
    ]);

    // Перевіряємо зв'язки в проміжних таблицях
    foreach ($tags as $tag) {
        $this->assertDatabaseHas('movie_tag', [
            'movie_id' => $movie->id,
            'tag_id' => $tag->id,
        ]);
    }

    // Перевіряємо зв'язки з персонами
    $this->assertDatabaseHas('movie_person', [
        'movie_id' => $movie->id,
        'person_id' => $persons[0]->id,
        'voice_person_id' => $voicePerson->id,
        'character_name' => 'Character 1'
    ]);

    $this->assertDatabaseHas('movie_person', [
        'movie_id' => $movie->id,
        'person_id' => $persons[1]->id,
        'character_name' => 'Character 2'
    ]);
});
