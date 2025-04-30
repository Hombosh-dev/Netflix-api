<?php

namespace Tests\Feature\Api\Auth\Comment;

use App\Models\Comment;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view comments', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create comments
    $comments = Comment::factory()->count(3)->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/comments');

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

    // Check that we have at least 3 comments in the response
    $this->assertGreaterThanOrEqual(3, count($response->json('data')));
});

test('authenticated user can filter comments by commentable', function () {
    // Arrange
    $user = User::factory()->create();
    $movie1 = Movie::factory()->create();
    $movie2 = Movie::factory()->create();

    // Create comments for movie1
    Comment::factory()->count(2)->create([
        'commentable_id' => $movie1->id,
        'commentable_type' => Movie::class,
    ]);

    // Create comments for movie2
    Comment::factory()->count(1)->create([
        'commentable_id' => $movie2->id,
        'commentable_type' => Movie::class,
    ]);

    // Act - filter by movie1
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments?commentable_type=" . urlencode(Movie::class) . "&commentable_id={$movie1->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we only have comments for movie1 in the response
    $comments = $response->json('data');
    foreach ($comments as $comment) {
        $this->assertEquals($movie1->id, $comment['commentable_id']);
        $this->assertEquals(Movie::class, $comment['commentable_type']);
    }

    // Check that we have 2 comments for movie1
    $this->assertCount(2, $comments);
});

test('authenticated user can filter comments by user', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create comments from user1
    Comment::factory()->count(2)->create([
        'user_id' => $user1->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create comments from user2
    Comment::factory()->count(1)->create([
        'user_id' => $user2->id,
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act - filter by user1
    $response = $this->actingAs($user1)
        ->getJson("/api/v1/comments?user_id={$user1->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we only have comments from user1 in the response
    $comments = $response->json('data');
    foreach ($comments as $comment) {
        $this->assertEquals($user1->id, $comment['user_id']);
    }

    // Check that we have 2 comments from user1
    $this->assertCount(2, $comments);
});

test('authenticated user can filter comments by parent', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create parent comment
    $parentComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create child comments
    Comment::factory()->count(2)->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'parent_id' => $parentComment->id
    ]);

    // Create another parent comment
    $anotherParentComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Act - filter by parent
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments?parent_id={$parentComment->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we only have comments with the specified parent in the response
    $comments = $response->json('data');
    foreach ($comments as $comment) {
        $this->assertEquals($parentComment->id, $comment['parent_id']);
    }

    // Check that we have 2 comments with the specified parent
    $this->assertCount(2, $comments);
});

test('authenticated user can filter comments by spoiler status', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create spoiler comments
    Comment::factory()->count(2)->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'is_spoiler' => true
    ]);

    // Create non-spoiler comments
    Comment::factory()->count(1)->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'is_spoiler' => false
    ]);

    // Act - filter by spoiler status
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments?is_spoiler=1");

    // Assert
    $response->assertStatus(200);

    // Check that we only have spoiler comments in the response
    $comments = $response->json('data');
    foreach ($comments as $comment) {
        $this->assertTrue($comment['is_spoiler']);
    }

    // Check that we have 2 spoiler comments
    $this->assertCount(2, $comments);
});

test('authenticated user can sort comments', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();

    // Create comments with different creation dates
    Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'created_at' => now()->subDays(2)
    ]);

    Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'created_at' => now()->subDays(1)
    ]);

    Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'created_at' => now()
    ]);

    // Act - sort by creation date ascending
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comments?sort=created_at&direction=asc");

    // Assert
    $response->assertStatus(200);

    // Check that comments are sorted by creation date in ascending order
    $comments = $response->json('data');
    $creationDates = array_column($comments, 'created_at');
    $sortedDates = $creationDates;
    sort($sortedDates);

    $this->assertEquals($sortedDates, $creationDates);
});

test('unauthenticated user cannot view comments', function () {
    // Act
    $response = $this->getJson('/api/v1/comments');

    // Assert
    $response->assertStatus(401);
});
