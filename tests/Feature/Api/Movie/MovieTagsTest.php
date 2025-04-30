<?php

use App\Models\Movie;
use App\Models\Studio;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('tags endpoint returns tags associated with a movie', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie',
        'is_published' => true
    ]);
    
    // Create tags and associate them with the movie
    $tag1 = Tag::factory()->create([
        'name' => 'Action',
        'image' => 'tags/action.jpg'
    ]);
    
    $tag2 = Tag::factory()->create([
        'name' => 'Adventure',
        'image' => 'tags/adventure.jpg'
    ]);
    
    $tag3 = Tag::factory()->create([
        'name' => 'Comedy',
        'image' => 'tags/comedy.jpg'
    ]);
    
    $movie->tags()->attach([$tag1->id, $tag2->id, $tag3->id]);

    // Act
    $response = $this->getJson("/api/v1/movies/{$movie->slug}/tags");

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'image',
                    'is_genre'
                ]
            ]
        ]);
    
    $tagNames = collect($response->json('data'))->pluck('name')->all();
    expect($tagNames)->toContain('Action')
        ->toContain('Adventure')
        ->toContain('Comedy');
});
