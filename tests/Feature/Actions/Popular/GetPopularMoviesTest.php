<?php

use App\Actions\Popular\GetPopularMovies;
use App\DTOs\Popular\PopularMoviesDTO;
use App\Enums\Kind;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns popular movies ordered by imdb score', function () {
    // Arrange
    Movie::factory()->create([
        'kind' => Kind::MOVIE,
        'imdb_score' => 8.5,
    ]);
    
    Movie::factory()->create([
        'kind' => Kind::MOVIE,
        'imdb_score' => 9.0,
    ]);
    
    Movie::factory()->create([
        'kind' => Kind::ANIMATED_MOVIE,
        'imdb_score' => 8.0,
    ]);
    
    $action = new GetPopularMovies();
    $dto = new PopularMoviesDTO(limit: 3);

    // Act
    $movies = $action->handle($dto);

    // Assert
    expect($movies)->toHaveCount(3)
        ->and($movies->first()->imdb_score)->toBe(9.0);
});

test('it respects the limit parameter', function () {
    // Arrange
    Movie::factory()->count(5)->create([
        'kind' => Kind::MOVIE,
    ]);
    
    $action = new GetPopularMovies();
    $dto = new PopularMoviesDTO(limit: 3);

    // Act
    $movies = $action->handle($dto);

    // Assert
    expect($movies)->toHaveCount(3);
});

test('it only returns movies and animated movies', function () {
    // Arrange
    Movie::factory()->create([
        'kind' => Kind::MOVIE,
    ]);
    
    Movie::factory()->create([
        'kind' => Kind::ANIMATED_MOVIE,
    ]);
    
    Movie::factory()->create([
        'kind' => Kind::TV_SERIES,
    ]);
    
    $action = new GetPopularMovies();
    $dto = new PopularMoviesDTO(limit: 10);

    // Act
    $movies = $action->handle($dto);

    // Assert
    expect($movies)->toHaveCount(2)
        ->and($movies->pluck('kind')->toArray())->toContain(Kind::MOVIE, Kind::ANIMATED_MOVIE)
        ->and($movies->pluck('kind')->toArray())->not->toContain(Kind::TV_SERIES);
});
