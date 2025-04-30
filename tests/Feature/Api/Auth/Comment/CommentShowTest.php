<?php

namespace Tests\Feature\Api\Auth\Comment;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view comment details', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user->id,
        'body' => 'This is a test comment',
        'is_spoiler' => true
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/{$comment->id}");

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
        ->assertJsonPath('data.commentable_id', $movie->id)
        ->assertJsonPath('data.commentable_type', Movie::class)
        ->assertJsonPath('data.body', 'This is a test comment')
        ->assertJsonPath('data.is_spoiler', true);
});

test('authenticated user can view another user comment details', function () {
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
        ->getJson("/api/v1/comments/{$comment->id}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $comment->id)
        ->assertJsonPath('data.user_id', $user2->id);
});

test('admin can view any comment details', function () {
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
        ->getJson("/api/v1/comments/{$comment->id}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $comment->id)
        ->assertJsonPath('data.user_id', $regularUser->id);
});

test('unauthenticated user cannot view comment details', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user->id
    ]);

    // Act
    $response = $this->getJson("/api/v1/comments/{$comment->id}");

    // Assert
    $response->assertStatus(401);
});

test('returns 404 when comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/non-existent-id");

    // Assert
    $response->assertStatus(404);
});
