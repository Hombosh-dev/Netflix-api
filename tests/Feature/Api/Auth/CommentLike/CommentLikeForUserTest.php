<?php

namespace Tests\Feature\Api\Auth\CommentLike;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their own likes', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes for the user for different comments
    $likes = [];
    for ($i = 0; $i < 3; $i++) {
        $newComment = Comment::factory()->create([
            'commentable_id' => $movie->id,
            'commentable_type' => Movie::class,
        ]);

        $likes[] = CommentLike::factory()->create([
            'user_id' => $user->id,
            'comment_id' => $newComment->id
        ]);
    }

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/user/{$user->id}");

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

    // Check that all likes belong to the user
    $responseData = $response->json('data');
    foreach ($responseData as $like) {
        $this->assertEquals($user->id, $like['user_id']);
    }
});

test('authenticated user cannot view another user likes', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes for user2 for different comments
    $likes = [];
    for ($i = 0; $i < 3; $i++) {
        $newComment = Comment::factory()->create([
            'commentable_id' => $movie->id,
            'commentable_type' => Movie::class,
        ]);

        $likes[] = CommentLike::factory()->create([
            'user_id' => $user2->id,
            'comment_id' => $newComment->id
        ]);
    }

    // Act
    $response = $this->actingAs($user1)
        ->getJson("/api/v1/comment-likes/user/{$user2->id}");

    // Assert
    $response->assertStatus(403)
        ->assertJson(['message' => 'You do not have permission to view likes for this user']);
});

test('admin can view any user likes', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes for the regular user for different comments
    $likes = [];
    for ($i = 0; $i < 3; $i++) {
        $newComment = Comment::factory()->create([
            'commentable_id' => $movie->id,
            'commentable_type' => Movie::class,
        ]);

        $likes[] = CommentLike::factory()->create([
            'user_id' => $regularUser->id,
            'comment_id' => $newComment->id
        ]);
    }

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/comment-likes/user/{$regularUser->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we have 3 likes in the response
    $this->assertCount(3, $response->json('data'));

    // Check that all likes belong to the regular user
    $responseData = $response->json('data');
    foreach ($responseData as $like) {
        $this->assertEquals($regularUser->id, $like['user_id']);
    }
});

test('authenticated user can filter their likes by like status', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes for different comments
    for ($i = 0; $i < 2; $i++) {
        $newComment = Comment::factory()->create([
            'commentable_id' => $movie->id,
            'commentable_type' => Movie::class,
        ]);

        CommentLike::factory()->create([
            'user_id' => $user->id,
            'comment_id' => $newComment->id,
            'is_liked' => true
        ]);
    }

    // Create dislikes for different comments
    $newComment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    CommentLike::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $newComment->id,
        'is_liked' => false
    ]);

    // Act - filter by likes
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/user/{$user->id}?is_liked=1");

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

test('returns empty array when user has no likes', function () {
    // Arrange
    $user = User::factory()->create();

    // Створюємо коментар, щоб переконатися, що користувач існує
    $movie = Movie::factory()->create();
    Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
        'user_id' => $user->id
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/user/{$user->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we have 0 likes in the response
    $this->assertCount(0, $response->json('data'));
});

test('returns 404 when user does not exist', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-likes/user/non-existent-id");

    // Assert
    $response->assertStatus(404);
});

test('unauthenticated user cannot view user likes', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);

    // Create likes for the user for different comments
    $likes = [];
    for ($i = 0; $i < 3; $i++) {
        $newComment = Comment::factory()->create([
            'commentable_id' => $movie->id,
            'commentable_type' => Movie::class,
        ]);

        $likes[] = CommentLike::factory()->create([
            'user_id' => $user->id,
            'comment_id' => $newComment->id
        ]);
    }

    // Act
    $response = $this->getJson("/api/v1/comment-likes/user/{$user->id}");

    // Assert
    $response->assertStatus(401);
});
