<?php

use App\Actions\Popular\GetPopularSeries;
use App\DTOs\Popular\PopularSeriesDTO;
use App\Enums\Kind;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns popular series ordered by imdb score', function () {
    // Arrange
    Movie::factory()->create([
        'kind' => Kind::TV_SERIES,
        'imdb_score' => 8.5,
    ]);
    
    Movie::factory()->create([
        'kind' => Kind::TV_SERIES,
        'imdb_score' => 9.0,
    ]);
    
    Movie::factory()->create([
        'kind' => Kind::ANIMATED_SERIES,
        'imdb_score' => 8.0,
    ]);
    
    $action = new GetPopularSeries();
    $dto = new PopularSeriesDTO(limit: 3);

    // Act
    $series = $action->handle($dto);

    // Assert
    expect($series)->toHaveCount(3)
        ->and($series->first()->imdb_score)->toBe(9.0);
});

test('it respects the limit parameter', function () {
    // Arrange
    Movie::factory()->count(5)->create([
        'kind' => Kind::TV_SERIES,
    ]);
    
    $action = new GetPopularSeries();
    $dto = new PopularSeriesDTO(limit: 3);

    // Act
    $series = $action->handle($dto);

    // Assert
    expect($series)->toHaveCount(3);
});

test('it only returns tv series and animated series', function () {
    // Arrange
    Movie::factory()->create([
        'kind' => Kind::TV_SERIES,
    ]);
    
    Movie::factory()->create([
        'kind' => Kind::ANIMATED_SERIES,
    ]);
    
    Movie::factory()->create([
        'kind' => Kind::MOVIE,
    ]);
    
    $action = new GetPopularSeries();
    $dto = new PopularSeriesDTO(limit: 10);

    // Act
    $series = $action->handle($dto);

    // Assert
    expect($series)->toHaveCount(2)
        ->and($series->pluck('kind')->toArray())->toContain(Kind::TV_SERIES, Kind::ANIMATED_SERIES)
        ->and($series->pluck('kind')->toArray())->not->toContain(Kind::MOVIE);
});
