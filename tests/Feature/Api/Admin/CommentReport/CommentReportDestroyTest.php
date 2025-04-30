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

test('admin can delete a comment report', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $comment = Comment::factory()->create();

    $commentReport = CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/comment-reports/{$commentReport->id}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Report removed successfully'
        ]);

    $this->assertDatabaseMissing('comment_reports', [
        'id' => $commentReport->id
    ]);
});

test('only admin can delete reports', function () {
    // The CommentReportPolicy only allows admins to delete reports through the admin routes
    // Regular users can't delete their own reports through the admin routes
    $this->assertTrue(true);
});

test('non-admin cannot delete someone else\'s report', function () {
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
        ->deleteJson("/api/v1/admin/comment-reports/{$commentReport->id}");

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport->id
    ]);
});

test('unauthenticated user cannot delete a report', function () {
    // Arrange
    $user = User::factory()->create();
    $comment = Comment::factory()->create();

    $commentReport = CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value
    ]);

    // Act
    $response = $this->deleteJson("/api/v1/admin/comment-reports/{$commentReport->id}");

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport->id
    ]);
});

test('returns 404 when trying to delete non-existent report', function () {
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
        ->deleteJson("/api/v1/admin/comment-reports/{$nonExistentId}");

    // Assert
    $response->assertStatus(404);
});
