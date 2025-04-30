<?php

namespace Tests\Feature\Api\Auth\CommentLike;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view comment likes', function () {
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
        ->getJson('/api/v1/comment-likes');

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

    // Check that we have at least 3 likes in the response
    $this->assertGreaterThanOrEqual(3, count($response->json('data')));
});

test('authenticated user can filter likes by comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create two comments
    $comment1 = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    $comment2 = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes for comment1 from different users
    for ($i = 0; $i < 2; $i++) {
        $likeUser = User::factory()->create();
        CommentLike::factory()->create([
            'user_id' => $likeUser->id,
            'comment_id' => $comment1->id
        ]);
    }

    // Create likes for comment2 from different users
    $likeUser = User::factory()->create();
    CommentLike::factory()->create([
        'user_id' => $likeUser->id,
        'comment_id' => $comment2->id
    ]);

    // Act - filter by comment1
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes?comment_id={$comment1->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we only have likes for comment1 in the response
    $likes = $response->json('data');
    foreach ($likes as $like) {
        $this->assertEquals($comment1->id, $like['comment_id']);
    }

    // Check that we have 2 likes for comment1
    $this->assertCount(2, $likes);
});

test('authenticated user can filter likes by user', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes from user1 for different comments
    for ($i = 0; $i < 2; $i++) {
        $newComment = Comment::factory()->create([
            'commentable_id' => $movie->id,
            'commentable_type' => Movie::class,
        ]);

        CommentLike::factory()->create([
            'user_id' => $user1->id,
            'comment_id' => $newComment->id
        ]);
    }

    // Create likes from user2
    $newComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    CommentLike::factory()->create([
        'user_id' => $user2->id,
        'comment_id' => $newComment->id
    ]);

    // Act - filter by user1
    $response = $this->actingAs($user1)
        ->getJson("/api/v1/comment-likes?user_id={$user1->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we only have likes from user1 in the response
    $likes = $response->json('data');
    foreach ($likes as $like) {
        $this->assertEquals($user1->id, $like['user_id']);
    }

    // Check that we have 2 likes from user1
    $this->assertCount(2, $likes);
});

test('authenticated user can filter likes by like status', function () {
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
        ->getJson("/api/v1/comment-likes?is_liked=1");

    // Assert
    $response->assertStatus(200);

    // Check that we only have likes in the response
    $likes = $response->json('data');
    foreach ($likes as $like) {
        $this->assertTrue($like['is_liked']);
    }

    // Check that we have 2 likes
    $this->assertCount(2, $likes);

    // Act - filter by dislikes
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes?is_liked=0");

    // Assert
    $response->assertStatus(200);

    // Check that we only have dislikes in the response
    $likes = $response->json('data');
    foreach ($likes as $like) {
        $this->assertFalse($like['is_liked']);
    }

    // Check that we have 1 dislike
    $this->assertCount(1, $likes);
});

test('unauthenticated user cannot view comment likes', function () {
    // Act
    $response = $this->getJson('/api/v1/comment-likes');

    // Assert
    $response->assertStatus(401);
});
