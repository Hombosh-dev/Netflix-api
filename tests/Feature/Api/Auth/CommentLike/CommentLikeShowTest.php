<?php

namespace Tests\Feature\Api\Auth\CommentLike;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view comment like details', function () {
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
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/{$commentLike->id}");
    
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
        ->assertJsonPath('data.is_liked', true);
});

test('authenticated user can view another user like details', function () {
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
    
    // Act
    $response = $this->actingAs($user1)
        ->getJson("/api/v1/comment-likes/{$commentLike->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $commentLike->id)
        ->assertJsonPath('data.user_id', $user2->id);
});

test('admin can view any comment like details', function () {
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
    
    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/comment-likes/{$commentLike->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $commentLike->id)
        ->assertJsonPath('data.user_id', $regularUser->id);
});

test('unauthenticated user cannot view comment like details', function () {
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
    
    // Act
    $response = $this->getJson("/api/v1/comment-likes/{$commentLike->id}");
    
    // Assert
    $response->assertStatus(401);
});

test('returns 404 when comment like does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/non-existent-id");
    
    // Assert
    $response->assertStatus(404);
});
