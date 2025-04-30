<?php

namespace Tests\Feature\Api\Auth\Comment;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view replies to a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $parentComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create replies to the parent comment
    $replies = Comment::factory()->count(3)->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'parent_id' => $parentComment->id
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/{$parentComment->id}/replies");

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

    // Check that we have 3 replies in the response
    $this->assertCount(3, $response->json('data'));

    // Check that all replies have the correct parent_id
    $responseData = $response->json('data');
    foreach ($responseData as $reply) {
        $this->assertEquals($parentComment->id, $reply['parent_id']);
    }
});

test('authenticated user can filter replies by spoiler status', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $parentComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create spoiler replies
    Comment::factory()->count(2)->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'parent_id' => $parentComment->id,
        'is_spoiler' => true
    ]);

    // Create non-spoiler replies
    Comment::factory()->count(1)->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'parent_id' => $parentComment->id,
        'is_spoiler' => false
    ]);

    // Act - filter by spoiler status
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/{$parentComment->id}/replies?is_spoiler=1");

    // Assert
    $response->assertStatus(200);

    // Check that we only have spoiler replies in the response
    $replies = $response->json('data');
    foreach ($replies as $reply) {
        $this->assertTrue($reply['is_spoiler']);
    }

    // Check that we have 2 spoiler replies
    $this->assertCount(2, $replies);
});

test('authenticated user can sort replies', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $parentComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create replies with different creation dates
    Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'parent_id' => $parentComment->id,
        'created_at' => now()->subDays(2)
    ]);

    Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'parent_id' => $parentComment->id,
        'created_at' => now()->subDays(1)
    ]);

    Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'parent_id' => $parentComment->id,
        'created_at' => now()
    ]);

    // Act - sort by creation date ascending
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/{$parentComment->id}/replies?sort=created_at&direction=asc");

    // Assert
    $response->assertStatus(200);

    // Check that replies are sorted by creation date in ascending order
    $replies = $response->json('data');
    $creationDates = array_column($replies, 'created_at');
    $sortedDates = $creationDates;
    sort($sortedDates);

    $this->assertEquals($sortedDates, $creationDates);
});

test('returns empty array when comment has no replies', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/{$comment->id}/replies");

    // Assert
    $response->assertStatus(200);

    // Check that we have 0 replies in the response
    $this->assertCount(0, $response->json('data'));
});

test('returns 404 when comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments/non-existent-id/replies");

    // Assert
    $response->assertStatus(404);
});

test('unauthenticated user cannot view replies to a comment', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $parentComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create replies to the parent comment
    $replies = Comment::factory()->count(3)->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'parent_id' => $parentComment->id
    ]);

    // Act
    $response = $this->getJson("/api/v1/comments/{$parentComment->id}/replies");

    // Assert
    $response->assertStatus(401);
});
