<?php

namespace Tests\Feature\Api\Auth\Comment;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can update their own comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user->id,
        'body' => 'Original comment',
        'is_spoiler' => false
    ]);

    $updateData = [
        'body' => 'Updated comment',
        'is_spoiler' => true
    ];

    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/comments/{$comment->id}", $updateData);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'commentable_id',
                'commentable_type',
                'parent_id',
                'body',
                'is_spoiler',
                'is_reply',
                'likes_count',
                'replies_count',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJsonPath('data.id', $comment->id)
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.body', 'Updated comment')
        ->assertJsonPath('data.is_spoiler', true);

    // Check that the comment was updated in the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'user_id' => $user->id,
        'body' => 'Updated comment',
        'is_spoiler' => true
    ]);
});

test('authenticated user cannot update another user comment', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user2->id,
        'body' => 'Original comment'
    ]);

    $updateData = [
        'body' => 'Updated comment'
    ];

    // Act
    $response = $this->actingAs($user1)
        ->putJson("/api/v1/comments/{$comment->id}", $updateData);

    // Assert
    $response->assertStatus(403);

    // Check that the comment was not updated in the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'user_id' => $user2->id,
        'body' => 'Original comment'
    ]);
});

test('admin can update any user comment', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $regularUser->id,
        'body' => 'Original comment'
    ]);

    $updateData = [
        'body' => 'Updated by admin'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/comments/{$comment->id}", $updateData);

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $comment->id)
        ->assertJsonPath('data.body', 'Updated by admin');

    // Check that the comment was updated in the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'user_id' => $regularUser->id,
        'body' => 'Updated by admin'
    ]);
});

test('validation fails when body is empty', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user->id,
        'body' => 'Original comment'
    ]);

    $updateData = [
        'body' => ''
    ];

    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/comments/{$comment->id}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['body']);

    // Check that the comment was not updated in the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Original comment'
    ]);
});

test('unauthenticated user cannot update a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user->id,
        'body' => 'Original comment'
    ]);

    $updateData = [
        'body' => 'Updated comment'
    ];

    // Act
    $response = $this->putJson("/api/v1/comments/{$comment->id}", $updateData);

    // Assert
    $response->assertStatus(401);

    // Check that the comment was not updated in the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Original comment'
    ]);
});

test('returns 404 when comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();

    $updateData = [
        'body' => 'Updated comment'
    ];

    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/comments/non-existent-id", $updateData);

    // Assert
    $response->assertStatus(404);
});
