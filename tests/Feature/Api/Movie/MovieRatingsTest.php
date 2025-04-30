<?php

use App\Models\Movie;
use App\Models\Studio;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('ratings endpoint returns ratings for a movie', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie',
        'is_published' => true
    ]);
    
    // Create users and ratings
    $user1 = User::factory()->create(['name' => 'User 1']);
    $user2 = User::factory()->create(['name' => 'User 2']);
    
    Rating::factory()->create([
        'user_id' => $user1->id,
        'movie_id' => $movie->id,
        'number' => 8
    ]);
    
    Rating::factory()->create([
        'user_id' => $user2->id,
        'movie_id' => $movie->id,
        'number' => 9
    ]);

    // Act
    $response = $this->getJson("/api/v1/movies/{$movie->slug}/ratings");

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'number'
                ]
            ]
        ]);
    
    $ratings = collect($response->json('data'))->pluck('number')->all();
    expect($ratings)->toContain(8)
        ->toContain(9);
});
