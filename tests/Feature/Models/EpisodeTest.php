<?php

use App\Models\Builders\EpisodeQueryBuilder;
use App\Models\Episode;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Групуємо тести для моделі Episode
test('episode has correct query builder', function () {
    expect(Episode::query())->toBeInstanceOf(EpisodeQueryBuilder::class);
});

test('episode has correct relationships', function () {
    $episode = new Episode();

    expect($episode->movie())->toBeInstanceOf(BelongsTo::class)
        ->and($episode->userLists())->toBeInstanceOf(MorphMany::class)
        ->and($episode->comments())->toBeInstanceOf(MorphMany::class);
});

test('episode has correct casts', function () {
    $episode = new Episode();
    $casts = $episode->getCasts();

    expect($casts)->toBeArray()
        ->toHaveKey('pictures')
        ->toHaveKey('video_players')
        ->toHaveKey('air_date')
        ->toHaveKey('is_filler')
        ->and($casts['air_date'])->toBe('date')
        ->and($casts['is_filler'])->toBe('boolean');
});

test('episode has correct accessors', function () {
    $episode = new Episode([
        'name' => 'Test Episode',
        'number' => 5,
        'duration' => 45,
        'pictures' => ['episodes/test.jpg'],
    ]);

    expect($episode->picturesUrl)->toBeArray()
        ->and($episode->picturesUrl[0])->toContain('storage/episodes/test.jpg')
        ->and($episode->formattedDuration)->toBe('45 хв')
        ->and($episode->fullName)->toBe('Episode 5: Test Episode');
});

test('episode query builder can filter by movie', function () {
    // Arrange
    $movieId = 'test-movie-id';

    // Act
    $query = Episode::forMovie($movieId);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"movie_id" = ?')
        ->and($query->getBindings())->toContain($movieId);
});

test('episode query builder can filter by air date', function () {
    // Arrange
    $date = Carbon::now()->subDays(7);

    // Act
    $query = Episode::airedAfter($date);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"air_date" >= ?')
        ->and($query->getBindings())->toContain($date);
});

test('episode query builder can filter fillers', function () {
    // Arrange & Act
    $query = Episode::fillers(false);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"is_filler" = ?')
        ->and($query->getBindings())->toContain(false);
});

test('episode query builder can get recently aired episodes', function () {
    // Arrange
    $days = 7;

    // Act
    $query = Episode::recentlyAired($days);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('"air_date" >= ?')
        ->and($sql)->toContain('order by "air_date" desc');
});

test('episode query builder can order by number', function () {
    // Arrange
    $direction = 'desc';

    // Act
    $query = Episode::orderByNumber($direction);
    $sql = $query->toSql();

    // Assert
    expect($sql)->toContain('order by "number" desc');
});

test('episode factory creates valid model', function () {
    // Act
    $episode = Episode::factory()->make();

    // Assert
    expect($episode)->toBeInstanceOf(Episode::class)
        ->and($episode->name)->not->toBeEmpty()
        ->and($episode->slug)->not->toBeEmpty()
        ->and($episode->number)->toBeGreaterThan(0);
});

// Додаємо тест для створення епізоду в базі даних
test('can create episode in database', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $pictures = ['episodes/test1.jpg', 'episodes/test2.jpg'];
    $videoPlayers = [
        [
            'name' => 'youtube',
            'url' => 'https://youtube.com/watch?v=test',
            'file_url' => 'https://youtube.com/test.mp4',
            'dubbing' => 'Українська',
            'quality' => 'hd',
            'locale_code' => 'uk'
        ]
    ];
    $metaTitle = 'E3: Test Episode Name | Test Movie';
    $metaDescription = 'Test meta description for episode';
    $metaImage = 'episodes/meta-image.jpg';

    // Act
    $episode = Episode::factory()
        ->for($movie)
        ->create([
            'name' => 'Test Episode Name',
            'slug' => 'test-episode-name',
            'number' => 3,
            'duration' => 42,
            'is_filler' => false,
            'air_date' => '2023-05-15',
            'description' => 'Test episode description',
            'pictures' => $pictures,
            'video_players' => $videoPlayers,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'meta_image' => $metaImage
        ]);

    // Assert
    expect($episode)->toBeInstanceOf(Episode::class)
        ->and($episode->exists)->toBeTrue()
        ->and($episode->name)->toBe('Test Episode Name')
        ->and($episode->slug)->toBe('test-episode-name')
        ->and($episode->number)->toBe(3)
        ->and($episode->duration)->toBe(42)
        ->and($episode->is_filler)->toBeFalse()
        ->and($episode->air_date->format('Y-m-d'))->toBe('2023-05-15')
        ->and($episode->description)->toBe('Test episode description')
        ->and($episode->movie_id)->toBe($movie->id)
        ->and($episode->pictures->toArray())->toBe($pictures)
        ->and($episode->video_players->toArray())->toBe($videoPlayers)
        ->and($episode->meta_title)->toBe($metaTitle)
        ->and($episode->meta_description)->toBe($metaDescription)
        ->and($episode->meta_image)->toBe($metaImage)
        // Перевіряємо, що епізод дійсно збережено в базі даних
        ->and(Episode::where('id', $episode->id)->exists())->toBeTrue();

    // Альтернативний спосіб перевірки наявності в базі даних
    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id,
        'name' => 'Test Episode Name',
        'slug' => 'test-episode-name',
        'number' => 3,
        'duration' => 42,
        'is_filler' => false,
        'movie_id' => $movie->id,
        'description' => 'Test episode description',
        'meta_title' => $metaTitle,
        'meta_description' => $metaDescription,
        'meta_image' => $metaImage
    ]);
});

// Додаємо тест для перевірки видалення епізоду при видаленні фільму
test('episode is deleted when movie is deleted', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $episode = Episode::factory()->for($movie)->create();

    // Act - видаляємо фільм
    $movie->delete();

    // Assert - перевіряємо, що епізод також видалений
    expect(Episode::where('id', $episode->id)->exists())->toBeFalse();

    // Альтернативний спосіб перевірки відсутності в базі даних
    $this->assertDatabaseMissing('episodes', [
        'id' => $episode->id,
    ]);
});

// Додаємо тест для перевірки оновлення епізоду
test('can update episode', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $episode = Episode::factory()->for($movie)->create([
        'name' => 'Original Name',
        'description' => 'Original description',
        'duration' => 30,
        'is_filler' => false,
    ]);

    // Act - оновлюємо епізод
    $episode->name = 'Updated Name';
    $episode->description = 'Updated description';
    $episode->duration = 45;
    $episode->is_filler = true;
    $episode->save();

    // Оновлюємо модель з бази даних
    $episode->refresh();

    // Assert
    expect($episode->name)->toBe('Updated Name')
        ->and($episode->description)->toBe('Updated description')
        ->and($episode->duration)->toBe(45)
        ->and($episode->is_filler)->toBeTrue();

    // Перевіряємо, що зміни збережені в базі даних
    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id,
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'duration' => 45,
        'is_filler' => true,
    ]);
});

// Додаємо тест для перевірки унікальності номера епізоду в межах одного фільму
test('episode number must be unique within a movie', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $episodeNumber = 5;

    // Створюємо перший епізод з номером 5
    Episode::factory()->for($movie)->create([
        'number' => $episodeNumber
    ]);

    // Act & Assert - перевіряємо, що не можна створити другий епізод з таким же номером для того ж фільму
    $this->expectException(\Illuminate\Database\QueryException::class);

    Episode::factory()->for($movie)->create([
        'number' => $episodeNumber
    ]);
});

// Додаємо тест для перевірки методу generateUniqueNumber
test('episode factory can generate unique number', function () {
    // Arrange
    $movie = Movie::factory()->create();

    // Створюємо кілька епізодів з послідовними номерами
    Episode::factory()->for($movie)->create(['number' => 1]);
    Episode::factory()->for($movie)->create(['number' => 2]);
    Episode::factory()->for($movie)->create(['number' => 3]);

    // Act - генеруємо наступний номер послідовно
    $nextNumber = Episode::factory()->generateUniqueNumber($movie->id, true);

    // Assert
    expect($nextNumber)->toBe(4);

    // Act - генеруємо випадковий номер, який ще не використовується
    $randomNumber = Episode::factory()->generateUniqueNumber($movie->id, false);

    // Assert - перевіряємо, що номер не збігається з існуючими
    expect($randomNumber)->not->toBeIn([1, 2, 3]);
});

// Додаємо тест для перевірки методу formatDuration
test('episode formats duration correctly', function () {
    // Arrange
    $episode1 = new Episode(['duration' => 45]);
    $episode2 = new Episode(['duration' => 90]);
    $episode3 = new Episode(['duration' => 150]);

    // Act & Assert
    expect($episode1->formattedDuration)->toBe('45 хв')
        ->and($episode2->formattedDuration)->toBe('1 год 30 хв')
        ->and($episode3->formattedDuration)->toBe('2 год 30 хв');
});
