<?php

namespace Tests\Feature\Api\Auth\Comment;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can delete their own comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user->id
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/comments/{$comment->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('message', 'Comment deleted successfully');
    
    // Check that the comment was deleted from the database
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id
    ]);
});

test('authenticated user cannot delete another user comment', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user2->id
    ]);
    
    // Act
    $response = $this->actingAs($user1)
        ->deleteJson("/api/v1/comments/{$comment->id}");
    
    // Assert
    $response->assertStatus(403);
    
    // Check that the comment was not deleted from the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id
    ]);
});

test('admin can delete any user comment', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $regularUser->id
    ]);
    
    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/comments/{$comment->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('message', 'Comment deleted successfully');
    
    // Check that the comment was deleted from the database
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id
    ]);
});

test('unauthenticated user cannot delete a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user->id
    ]);
    
    // Act
    $response = $this->deleteJson("/api/v1/comments/{$comment->id}");
    
    // Assert
    $response->assertStatus(401);
    
    // Check that the comment was not deleted from the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id
    ]);
});

test('returns 404 when comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/comments/non-existent-id");
    
    // Assert
    $response->assertStatus(404);
});
