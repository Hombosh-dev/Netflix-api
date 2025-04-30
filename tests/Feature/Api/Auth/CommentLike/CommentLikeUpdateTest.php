<?php

namespace Tests\Feature\Api\Auth\CommentLike;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can update their own like', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    $commentLike = CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);
    
    $updateData = [
        'is_liked' => false
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/comment-likes/{$commentLike->id}", $updateData);
    
    // Assert
    $response->assertStatus(200)
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
        ->assertJsonPath('data.id', $commentLike->id)
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.comment_id', $comment->id)
        ->assertJsonPath('data.is_liked', false);
    
    // Check that the like was updated in the database
    $this->assertDatabaseHas('comment_likes', [
        'id' => $commentLike->id,
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => false
    ]);
});

test('authenticated user cannot update another user like', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    $commentLike = CommentLike::factory()->create([
        'user_id' => $user2->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);
    
    $updateData = [
        'is_liked' => false
    ];
    
    // Act
    $response = $this->actingAs($user1)
        ->putJson("/api/v1/comment-likes/{$commentLike->id}", $updateData);
    
    // Assert
    $response->assertStatus(403);
    
    // Check that the like was not updated in the database
    $this->assertDatabaseHas('comment_likes', [
        'id' => $commentLike->id,
        'user_id' => $user2->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);
});

test('admin can update any user like', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    $commentLike = CommentLike::factory()->create([
        'user_id' => $regularUser->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);
    
    $updateData = [
        'is_liked' => false
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/comment-likes/{$commentLike->id}", $updateData);
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $commentLike->id)
        ->assertJsonPath('data.is_liked', false);
    
    // Check that the like was updated in the database
    $this->assertDatabaseHas('comment_likes', [
        'id' => $commentLike->id,
        'user_id' => $regularUser->id,
        'comment_id' => $comment->id,
        'is_liked' => false
    ]);
});

test('unauthenticated user cannot update a like', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    $commentLike = CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);
    
    $updateData = [
        'is_liked' => false
    ];
    
    // Act
    $response = $this->putJson("/api/v1/comment-likes/{$commentLike->id}", $updateData);
    
    // Assert
    $response->assertStatus(401);
    
    // Check that the like was not updated in the database
    $this->assertDatabaseHas('comment_likes', [
        'id' => $commentLike->id,
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'is_liked' => true
    ]);
});

test('returns 404 when comment like does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    $updateData = [
        'is_liked' => false
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/comment-likes/non-existent-id", $updateData);
    
    // Assert
    $response->assertStatus(404);
});
