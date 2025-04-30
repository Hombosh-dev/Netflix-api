<?php

use App\Models\Movie;
use App\Models\Studio;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('show endpoint returns detailed movie information', function () {
    // Arrange
    $studio = Studio::factory()->create(['name' => 'Test Studio']);
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie',
        'description' => 'This is a test movie description',
        'is_published' => true
    ]);
    
    // Create some tags for the movie
    $tag1 = Tag::factory()->create(['name' => 'Action']);
    $tag2 = Tag::factory()->create(['name' => 'Adventure']);
    $movie->tags()->attach([$tag1->id, $tag2->id]);

    // Act
    $response = $this->getJson("/api/v1/movies/{$movie->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'description',
                'kind',
                'status',
                'poster',
                'imdb_score',
                'year',
                'studio' => [
                    'id',
                    'name',
                    'slug'
                ],
                'tags' => [
                    '*' => [
                        'id',
                        'name',
                        'slug'
                    ]
                ]
            ]
        ])
        ->assertJsonPath('data.name', 'Test Movie')
        ->assertJsonPath('data.description', 'This is a test movie description')
        ->assertJsonPath('data.studio.name', 'Test Studio')
        ->assertJsonCount(2, 'data.tags');
});

test('show endpoint returns 404 for non-existent movie', function () {
    // Act
    $response = $this->getJson('/api/v1/movies/non-existent-movie');

    // Assert
    $response->assertStatus(404);
});
