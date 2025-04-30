<?php

namespace Tests\Feature\Api\Auth\CommentLike;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can like a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $likeData = [
        'comment_id' => $comment->id,
        'is_liked' => true
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-likes', $likeData);

    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'comment_id',
                'is_liked',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.comment_id', $comment->id)
        ->assertJsonPath('data.is_liked', true);

    // Check that the like was created in the database
    $this->assertDatabaseHas('comment_likes', [
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);
});

test('authenticated user can dislike a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $likeData = [
        'comment_id' => $comment->id,
        'is_liked' => false
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-likes', $likeData);

    // Assert
    $response->assertStatus(201)
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.comment_id', $comment->id)
        ->assertJsonPath('data.is_liked', false);

    // Check that the dislike was created in the database
    $this->assertDatabaseHas('comment_likes', [
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => false
    ]);
});

test('authenticated user can change like to dislike', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create an existing like
    $existingLike = CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);

    $likeData = [
        'comment_id' => $comment->id,
        'is_liked' => false
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-likes', $likeData);

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $existingLike->id)
        ->assertJsonPath('data.is_liked', false);

    // Check that the like was updated in the database
    $this->assertDatabaseHas('comment_likes', [
        'id' => $existingLike->id,
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => false
    ]);

    // Check that no new like was created
    $this->assertDatabaseCount('comment_likes', 1);
});

test('authenticated user cannot like the same comment twice', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create an existing like
    CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);

    $likeData = [
        'comment_id' => $comment->id,
        'is_liked' => true
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-likes', $likeData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonPath('message', 'You have already liked this comment');

    // Check that no new like was created
    $this->assertDatabaseCount('comment_likes', 1);
});

test('validation fails when required fields are missing', function () {
    // Arrange
    $user = User::factory()->create();

    $likeData = [
        // Missing required fields
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-likes', $likeData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['comment_id']);
});

test('validation fails when comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();

    $likeData = [
        'comment_id' => 'non-existent-id',
        'is_liked' => true
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-likes', $likeData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['comment_id']);
});

test('unauthenticated user cannot like a comment', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $likeData = [
        'comment_id' => $comment->id,
        'is_liked' => true
    ];

    // Act
    $response = $this->postJson('/api/v1/comment-likes', $likeData);

    // Assert
    $response->assertStatus(401);
});
