<?php

use App\Models\Movie;
use App\Models\Studio;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('comments endpoint returns comments for a movie', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie',
        'is_published' => true
    ]);
    
    // Create users and comments
    $user1 = User::factory()->create(['name' => 'User 1']);
    $user2 = User::factory()->create(['name' => 'User 2']);
    
    Comment::factory()->create([
        'user_id' => $user1->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'Great movie!'
    ]);
    
    Comment::factory()->create([
        'user_id' => $user2->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'I loved it!'
    ]);

    // Act
    $response = $this->getJson("/api/v1/movies/{$movie->slug}/comments");

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'body'
                ]
            ]
        ]);
    
    $comments = collect($response->json('data'))->pluck('body')->all();
    expect($comments)->toContain('Great movie!')
        ->toContain('I loved it!');
});
