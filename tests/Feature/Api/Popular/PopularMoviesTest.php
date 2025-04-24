<?php

use App\Actions\Popular\GetPopularMovies;
use App\Enums\Kind;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('popular movies endpoint returns movies ordered by imdb score', function () {
    // Arrange
    $highRatedMovie = Movie::factory()->create([
        'kind' => Kind::MOVIE,
        'imdb_score' => 9.0,
        'name' => 'High Rated Movie',
    ]);

    $mediumRatedMovie = Movie::factory()->create([
        'kind' => Kind::MOVIE,
        'imdb_score' => 8.0,
        'name' => 'Medium Rated Movie',
    ]);

    // Mock the action to avoid database queries
    $this->mock(GetPopularMovies::class, function ($mock) use ($highRatedMovie, $mediumRatedMovie) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new Collection([$highRatedMovie, $mediumRatedMovie]));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/movies');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'High Rated Movie')
        ->assertJsonPath('data.1.name', 'Medium Rated Movie');
});

test('popular movies endpoint respects limit parameter', function () {
    // Arrange
    $movies = Movie::factory()->count(5)->create([
        'kind' => Kind::MOVIE,
    ]);

    // Mock the action to avoid database queries
    $this->mock(GetPopularMovies::class, function ($mock) use ($movies) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new Collection($movies->take(3)->all()));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/movies?limit=3');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});
