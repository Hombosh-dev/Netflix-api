<?php

use App\Enums\Kind;
use App\Models\Movie;
use App\Models\Studio;
use App\Models\Episode;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('episodes endpoint returns episodes for a movie', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Series',
        'kind' => Kind::TV_SERIES,
        'is_published' => true
    ]);

    // Create episodes for the movie with unique numbers
    Episode::factory()->create([
        'movie_id' => $movie->id,
        'number' => 1
    ]);

    Episode::factory()->create([
        'movie_id' => $movie->id,
        'number' => 2
    ]);

    Episode::factory()->create([
        'movie_id' => $movie->id,
        'number' => 3
    ]);

    // Act
    $response = $this->getJson("/api/v1/movies/{$movie->slug}/episodes");

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'number',
                    'movie_id',
                    'air_date',
                    'picture_url',
                    'pictures',
                    'pictures_url',
                    'video_players',
                    'meta_title',
                    'meta_description',
                    'meta_image',
                    'meta_image_url',
                    'comments_count'
                ]
            ]
        ]);

    // Check that all episodes belong to the correct movie
    foreach ($response->json('data') as $episode) {
        expect($episode['movie_id'])->toBe($movie->id);
    }
});
