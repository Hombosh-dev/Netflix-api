<?php

namespace Tests\Feature\Api\Auth\CommentLike;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view likes for a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes for the comment from different users
    $likes = [];
    for ($i = 0; $i < 3; $i++) {
        $likeUser = User::factory()->create();
        $likes[] = CommentLike::factory()->create([
            'user_id' => $likeUser->id,
            'comment_id' => $comment->id
        ]);
    }

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/comment/{$comment->id}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'comment_id',
                    'is_liked',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links',
            'meta'
        ]);

    // Check that we have 3 likes in the response
    $this->assertCount(3, $response->json('data'));

    // Check that all likes belong to the comment
    $responseData = $response->json('data');
    foreach ($responseData as $like) {
        $this->assertEquals($comment->id, $like['comment_id']);
    }
});

test('authenticated user can filter likes for a comment by like status', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes from different users
    for ($i = 0; $i < 2; $i++) {
        $likeUser = User::factory()->create();
        CommentLike::factory()->create([
            'user_id' => $likeUser->id,
            'comment_id' => $comment->id,
            'is_liked' => true
        ]);
    }

    // Create dislikes from different users
    $dislikeUser = User::factory()->create();
    CommentLike::factory()->create([
        'user_id' => $dislikeUser->id,
        'comment_id' => $comment->id,
        'is_liked' => false
    ]);

    // Act - filter by likes
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/comment/{$comment->id}?is_liked=1");

    // Assert
    $response->assertStatus(200);

    // Check that we only have likes in the response
    $likes = $response->json('data');
    foreach ($likes as $like) {
        $this->assertTrue($like['is_liked']);
    }

    // Check that we have 2 likes
    $this->assertCount(2, $likes);
});

test('returns empty array when comment has no likes', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/comment/{$comment->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we have 0 likes in the response
    $this->assertCount(0, $response->json('data'));
});

test('returns 404 when comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/comment/non-existent-id");

    // Assert
    $response->assertStatus(404);
});

test('unauthenticated user cannot view likes for a comment', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes for the comment from different users
    $likes = [];
    for ($i = 0; $i < 3; $i++) {
        $likeUser = User::factory()->create();
        $likes[] = CommentLike::factory()->create([
            'user_id' => $likeUser->id,
            'comment_id' => $comment->id
        ]);
    }

    // Act
    $response = $this->getJson("/api/v1/comment-likes/comment/{$comment->id}");

    // Assert
    $response->assertStatus(401);
});
