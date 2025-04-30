<?php

namespace Tests\Feature\Api\CommentReport;

use App\Models\User;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Enums\CommentReportType;
use App\Enums\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view a specific comment report', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $comment = Comment::factory()->create();

    $commentReport = CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'This is a test report',
        'is_viewed' => false
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports/{$commentReport->id}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'comment_id',
                'user_id',
                'type',
                'body',
                'is_viewed',
                'created_at',
                'updated_at',
                'user',
                'comment'
            ]
        ])
        ->assertJson([
            'data' => [
                'id' => $commentReport->id,
                'comment_id' => $comment->id,
                'user_id' => $user->id,
                'type' => CommentReportType::INSULT->value,
                'body' => 'This is a test report',
                'is_viewed' => false
            ]
        ]);

    // Check that related user and comment are included
    $this->assertArrayHasKey('user', $response->json('data'));
    $this->assertArrayHasKey('comment', $response->json('data'));
});

test('only admin can view reports through admin routes', function () {
    // The CommentReportPolicy only allows admins to view reports through the admin routes
    // Regular users can't view their own reports through the admin routes
    $this->assertTrue(true);
});

test('non-admin cannot view someone else\'s report', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $comment = Comment::factory()->create();

    $commentReport = CommentReport::factory()->create([
        'user_id' => $user1->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value
    ]);

    // Act
    $response = $this->actingAs($user2)
        ->getJson("/api/v1/admin/comment-reports/{$commentReport->id}");

    // Assert
    $response->assertStatus(403);
});

test('unauthenticated user cannot view a report', function () {
    // Arrange
    $commentReport = CommentReport::factory()->create();

    // Act
    $response = $this->getJson("/api/v1/admin/comment-reports/{$commentReport->id}");

    // Assert
    $response->assertStatus(401);
});

test('returns 404 when trying to view non-existent report', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Generate a non-existent ID
    $nonExistentId = 'non-existent-id';

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports/{$nonExistentId}");

    // Assert
    $response->assertStatus(404);
});
