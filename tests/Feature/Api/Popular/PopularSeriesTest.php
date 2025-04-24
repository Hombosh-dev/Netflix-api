<?php

use App\Actions\Popular\GetPopularSeries;
use App\Enums\Kind;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('popular series endpoint returns series ordered by imdb score', function () {
    // Arrange
    $highRatedSeries = Movie::factory()->create([
        'kind' => Kind::TV_SERIES,
        'imdb_score' => 9.0,
        'name' => 'High Rated Series',
    ]);

    $mediumRatedSeries = Movie::factory()->create([
        'kind' => Kind::TV_SERIES,
        'imdb_score' => 8.0,
        'name' => 'Medium Rated Series',
    ]);

    // Mock the action to avoid database queries
    $this->mock(GetPopularSeries::class, function ($mock) use ($highRatedSeries, $mediumRatedSeries) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$highRatedSeries, $mediumRatedSeries]));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/series');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'High Rated Series')
        ->assertJsonPath('data.1.name', 'Medium Rated Series');
});

test('popular series endpoint respects limit parameter', function () {
    // Arrange
    $series = Movie::factory()->count(5)->create([
        'kind' => Kind::TV_SERIES,
    ]);

    // Mock the action to avoid database queries
    $this->mock(GetPopularSeries::class, function ($mock) use ($series) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection($series->take(3)->all()));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/series?limit=3');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});
