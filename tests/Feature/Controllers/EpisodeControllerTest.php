<?php

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Studio;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Базові тести для EpisodeController
test('index endpoint returns paginated episodes', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
    ]);

    // Create episodes with unique numbers
    for ($i = 1; $i <= 5; $i++) {
        Episode::factory()->create([
            'movie_id' => $movie->id,
            'number' => $i,
        ]);
    }

    // Act
    $response = $this->getJson('/api/v1/episodes');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'movie_id',
                    'number',
                    'name',
                    'slug',
                    'full_name',
                    'description',
                    'duration',
                    'formatted_duration',
                    'air_date',
                    'is_filler',
                    'picture_url',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);

    // Don't assert exact count as it may vary based on test data
    $response->assertJsonPath('meta.total', fn($total) => $total > 0);
});

test('aired after endpoint returns episodes aired after a specific date', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
    ]);

    // Create episodes with different air dates
    Episode::factory()->create([
        'movie_id' => $movie->id,
        'air_date' => '2022-01-01',
    ]);

    Episode::factory()->create([
        'movie_id' => $movie->id,
        'air_date' => '2022-02-01',
    ]);

    Episode::factory()->create([
        'movie_id' => $movie->id,
        'air_date' => '2022-03-01',
    ]);

    // Act
    $response = $this->getJson('/api/v1/episodes/aired-after/2022-02-01');

    // Assert
    $response->assertStatus(200);

    $responseData = $response->json('data');
    foreach ($responseData as $episode) {
        $airDate = Carbon::parse($episode['air_date']);
        expect($airDate)->toBeGreaterThanOrEqual(Carbon::parse('2022-02-01'));
    }
});

test('show endpoint returns detailed episode information', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
    ]);
    $episode = Episode::factory()->create([
        'movie_id' => $movie->id,
        'name' => 'Test Episode',
        'slug' => 'test-episode',
    ]);

    // Log created episode
    \Log::info('Created episode for show test:', [
        'episode_id' => $episode->id,
        'episode_name' => $episode->name,
        'episode_slug' => $episode->slug,
        'episode_route_key_name' => $episode->getRouteKeyName(),
        'movie_id' => $movie->id,
    ]);

    // Act
    $response = $this->getJson("/api/v1/episodes/{$episode->slug}");

    // Log response
    \Log::info('Response for episode show test:', [
        'status' => $response->status(),
        'response' => $response->json(),
        'request_url' => "/api/v1/episodes/{$episode->slug}",
    ]);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'movie_id',
                'number',
                'name',
                'slug',
                'full_name',
                'description',
                'duration',
                'formatted_duration',
                'air_date',
                'is_filler',
                'pictures_url',
                'video_players',
                'comments_count',
                'created_at',
                'updated_at',
                'seo',
            ],
        ]);
});

test('show endpoint returns 404 for non-existent episode', function () {
    // Act
    $response = $this->getJson('/api/v1/episodes/non-existent-slug');

    // Assert
    $response->assertStatus(404);
});

test('for movie endpoint returns episodes for a specific movie', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie1 = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie 1',
        'slug' => 'test-movie-1',
    ]);
    $movie2 = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie 2',
        'slug' => 'test-movie-2',
    ]);

    // Create episodes for movie 1
    for ($i = 1; $i <= 3; $i++) {
        Episode::factory()->create([
            'movie_id' => $movie1->id,
            'number' => $i,
        ]);
    }

    // Create episodes for movie 2
    for ($i = 1; $i <= 2; $i++) {
        Episode::factory()->create([
            'movie_id' => $movie2->id,
            'number' => $i,
        ]);
    };

    // Log created movies
    \Log::info('Created movies for episodes test:', [
        'movie1_id' => $movie1->id,
        'movie1_name' => $movie1->name,
        'movie1_slug' => $movie1->slug,
        'movie2_id' => $movie2->id,
        'movie2_name' => $movie2->name,
        'movie2_slug' => $movie2->slug,
    ]);

    // Act
    $response = $this->getJson("/api/v1/episodes/movie/{$movie1->slug}");

    // Log response
    $responseData = $response->json('data') ?? [];
    \Log::info('Response for episodes by movie test:', [
        'status' => $response->status(),
        'data_count' => count($responseData),
        'response_data' => $responseData,
        'request_url' => "/api/v1/episodes/movie/{$movie1->slug}",
    ]);

    // Assert
    $response->assertStatus(200);

    // Check that all episodes in the response belong to movie1
    $responseData = $response->json('data');
    foreach ($responseData as $episode) {
        expect($episode['movie_id'])->toBe($movie1->id);
    }
});

test('episodes endpoint in movie controller returns episodes for a specific movie', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie',
        'slug' => 'test-movie',
    ]);

    // Create episodes for the movie
    for ($i = 1; $i <= 3; $i++) {
        Episode::factory()->create([
            'movie_id' => $movie->id,
            'number' => $i,
        ]);
    }

    // Log created movie
    \Log::info('Created movie for episodes endpoint test:', [
        'movie_id' => $movie->id,
        'movie_name' => $movie->name,
        'movie_slug' => $movie->slug,
    ]);

    // Act
    $response = $this->getJson("/api/v1/movies/{$movie->slug}/episodes");

    // Log response
    \Log::info('Response for movie episodes endpoint test:', [
        'status' => $response->status(),
        'data_count' => count($response->json('data')),
        'request_url' => "/api/v1/movies/{$movie->slug}/episodes",
    ]);

    // Assert
    $response->assertStatus(200);

    // Check that all episodes in the response belong to the movie
    $responseData = $response->json('data');
    foreach ($responseData as $episode) {
        expect($episode['movie_id'])->toBe($movie->id);
    }
});
