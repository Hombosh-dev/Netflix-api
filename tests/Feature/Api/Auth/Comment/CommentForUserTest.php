<?php

namespace Tests\Feature\Api\Auth\Comment;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view comments for a user', function () {
    // Arrange
    $user = User::factory()->create();
    $targetUser = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create comments for the target user
    $comments = Comment::factory()->count(3)->create([
        'user_id' => $targetUser->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/user/{$targetUser->id}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
            'links',
            'meta'
        ]);

    // Check that we have 3 comments in the response
    $this->assertCount(3, $response->json('data'));

    // Check that all comments belong to the target user
    $responseData = $response->json('data');
    foreach ($responseData as $comment) {
        $this->assertEquals($targetUser->id, $comment['user_id']);
    }
});

test('authenticated user can view their own comments', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create comments for the user
    $comments = Comment::factory()->count(3)->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/user/{$user->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we have 3 comments in the response
    $this->assertCount(3, $response->json('data'));

    // Check that all comments belong to the user
    $responseData = $response->json('data');
    foreach ($responseData as $comment) {
        $this->assertEquals($user->id, $comment['user_id']);
    }
});

test('admin can view any user comments', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create comments for the regular user
    $comments = Comment::factory()->count(3)->create([
        'user_id' => $regularUser->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/comments/user/{$regularUser->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we have 3 comments in the response
    $this->assertCount(3, $response->json('data'));

    // Check that all comments belong to the regular user
    $responseData = $response->json('data');
    foreach ($responseData as $comment) {
        $this->assertEquals($regularUser->id, $comment['user_id']);
    }
});

test('returns empty array when user has no comments', function () {
    // Arrange
    $user = User::factory()->create();
    $targetUser = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/user/{$targetUser->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we have 0 comments in the response
    $this->assertCount(0, $response->json('data'));
});

test('returns 404 when user does not exist', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/user/non-existent-id");

    // Assert
    $response->assertStatus(404);
});

test('unauthenticated user cannot view user comments', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create comments for the user
    $comments = Comment::factory()->count(3)->create([
        'user_id' => $user->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $response = $this->getJson("/api/v1/comments/user/{$user->id}");

    // Assert
    $response->assertStatus(401);
});
