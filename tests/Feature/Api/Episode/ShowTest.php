<?php

namespace Tests\Feature\Api\Episode;

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Studio;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('show endpoint returns correct episode details', function () {
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
    \Log::info('Created episode for show endpoint test:', [
        'episode_id' => $episode->id,
        'episode_name' => $episode->name,
        'episode_slug' => $episode->slug,
        'movie_id' => $movie->id,
    ]);

    // Act
    $response = $this->getJson("/api/v1/episodes/{$episode->slug}");

    // Log response
    \Log::info('Response from episode show endpoint:', [
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

    expect($response->json('data.id'))->toBe($episode->id);
});

test('show endpoint returns 404 for non-existent episode', function () {
    // Arrange
    $nonExistentSlug = 'non-existent-episode';

    // Log test attempt
    \Log::info('Attempting to get non-existent episode:', [
        'slug' => $nonExistentSlug,
    ]);

    // Act
    $response = $this->getJson("/api/v1/episodes/{$nonExistentSlug}");

    // Log response
    \Log::info('Response for non-existent episode:', [
        'status' => $response->status(),
    ]);

    // Assert
    $response->assertStatus(404);
});