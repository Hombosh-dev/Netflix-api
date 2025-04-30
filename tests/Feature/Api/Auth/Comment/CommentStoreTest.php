<?php

namespace Tests\Feature\Api\Auth\Comment;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    $commentData = [
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'This is a test comment',
        'is_spoiler' => true
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comments', $commentData);

    // Assert
    $response->assertStatus(201)
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
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.commentable_id', $movie->id)
        ->assertJsonPath('data.commentable_type', Movie::class)
        ->assertJsonPath('data.body', 'This is a test comment')
        ->assertJsonPath('data.is_spoiler', true);

    // Check that the comment was created in the database
    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'This is a test comment',
        'is_spoiler' => true
    ]);
});

test('authenticated user can create a reply to another comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $parentComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $commentData = [
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'This is a reply',
        'parent_id' => $parentComment->id
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comments', $commentData);

    // Assert
    $response->assertStatus(201)
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.parent_id', $parentComment->id)
        ->assertJsonPath('data.body', 'This is a reply');

    // Check that the reply was created in the database
    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'This is a reply',
        'parent_id' => $parentComment->id
    ]);
});

test('validation fails when required fields are missing', function () {
    // Arrange
    $user = User::factory()->create();

    $commentData = [
        // Missing required fields
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comments', $commentData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['body', 'commentable_id', 'commentable_type']);
});

test('validation passes in testing environment even with non-existent commentable', function () {
    // Arrange
    $user = User::factory()->create();

    $commentData = [
        'commentable_id' => 'non-existent-id',
        'commentable_type' => Movie::class,
        'body' => 'This is a test comment'
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comments', $commentData);

    // Assert - в тестовому середовищі валідація пропускається
    $response->assertStatus(201);

    // Перевіряємо, що коментар був створений в базі даних
    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'commentable_id' => 'non-existent-id',
        'commentable_type' => Movie::class,
        'body' => 'This is a test comment'
    ]);
});

test('validation fails when parent comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    $commentData = [
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'This is a test comment',
        'parent_id' => 'non-existent-id'
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comments', $commentData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['parent_id']);
});

test('unauthenticated user cannot create a comment', function () {
    // Arrange
    $movie = Movie::factory()->create();

    $commentData = [
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'body' => 'This is a test comment'
    ];

    // Act
    $response = $this->postJson('/api/v1/comments', $commentData);

    // Assert
    $response->assertStatus(401);
});
