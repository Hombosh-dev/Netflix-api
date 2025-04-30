<?php

namespace Tests\Feature\Api\Auth\CommentLike;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can delete their own like', function () {
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
        ->deleteJson("/api/v1/comment-likes/{$commentLike->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('message', 'Like removed successfully');
    
    // Check that the like was deleted from the database
    $this->assertDatabaseMissing('comment_likes', [
        'id' => $commentLike->id
    ]);
});

test('authenticated user cannot delete another user like', function () {
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
        ->deleteJson("/api/v1/comment-likes/{$commentLike->id}");
    
    // Assert
    $response->assertStatus(403);
    
    // Check that the like was not deleted from the database
    $this->assertDatabaseHas('comment_likes', [
        'id' => $commentLike->id
    ]);
});

test('admin can delete any user like', function () {
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
        ->deleteJson("/api/v1/comment-likes/{$commentLike->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('message', 'Like removed successfully');
    
    // Check that the like was deleted from the database
    $this->assertDatabaseMissing('comment_likes', [
        'id' => $commentLike->id
    ]);
});

test('unauthenticated user cannot delete a like', function () {
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
    $response = $this->deleteJson("/api/v1/comment-likes/{$commentLike->id}");
    
    // Assert
    $response->assertStatus(401);
    
    // Check that the like was not deleted from the database
    $this->assertDatabaseHas('comment_likes', [
        'id' => $commentLike->id
    ]);
});

test('returns 404 when comment like does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/comment-likes/non-existent-id");
    
    // Assert
    $response->assertStatus(404);
});
