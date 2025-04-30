<?php

namespace Tests\Feature\Api\Episode;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Studio;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('episodes by movie endpoint returns correct episodes', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie1 = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'First Movie',
        'slug' => 'first-movie',
    ]);

    $movie2 = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Second Movie',
        'slug' => 'second-movie',
    ]);

    // Create episodes for first movie
    for ($i = 1; $i <= 3; $i++) {
        Episode::factory()->create([
            'movie_id' => $movie1->id,
            'number' => $i,
        ]);
    }

    // Create episode for second movie
    Episode::factory()->create([
        'movie_id' => $movie2->id,
        'number' => 1,
    ]);

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
    // Use the movies/{movie}/episodes endpoint with slug instead of ID
    $response = $this->getJson("/api/v1/movies/{$movie1->slug}/episodes");

    // Log response
    $responseData = $response->json('data') ?? [];
    \Log::info('Response for episodes by movie test:', [
        'status' => $response->status(),
        'data_count' => count($responseData),
        'response_data' => $responseData,
        'request_url' => "/api/v1/movies/{$movie1->slug}/episodes",
    ]);

    // Assert
    $response->assertStatus(200);

    $responseData = $response->json('data');
    expect(count($responseData))->toBe(3);

    foreach ($responseData as $episode) {
        expect($episode['movie_id'])->toBe($movie1->id);
    }
});