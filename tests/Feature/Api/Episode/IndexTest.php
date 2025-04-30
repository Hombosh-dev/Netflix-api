<?php

namespace Tests\Feature\Api\Episode;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Studio;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index endpoint returns paginated episodes', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie',
        'slug' => 'test-movie',
    ]);

    // Create episodes with unique numbers
    for ($i = 1; $i <= 5; $i++) {
        Episode::factory()->create([
            'movie_id' => $movie->id,
            'number' => $i,
        ]);
    }

    // Log created episodes
    \Log::info('Created episodes for index endpoint test:', [
        'movie_id' => $movie->id,
        'episodes_count' => 5,
    ]);

    // Act
    $response = $this->getJson('/api/v1/episodes');

    // Log response
    \Log::info('Response from episodes index endpoint:', [
        'status' => $response->status(),
        'data_count' => count($response->json('data')),
    ]);

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

    expect($response->json('meta.total'))->toBeGreaterThan(0);
});