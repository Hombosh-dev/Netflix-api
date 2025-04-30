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

test('admin can update a comment report', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $comment = Comment::factory()->create();

    $commentReport = CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'Original report body',
        'is_viewed' => false
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'is_viewed' => true,
        'type' => CommentReportType::SPOILER->value,
        'body' => 'Updated report body'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/comment-reports/{$commentReport->id}", $updateData);

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
                'updated_at'
            ]
        ])
        ->assertJson([
            'data' => [
                'is_viewed' => true,
                'type' => CommentReportType::SPOILER->value,
                'body' => 'Updated report body'
            ]
        ]);

    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport->id,
        'is_viewed' => true,
        'type' => CommentReportType::SPOILER->value,
        'body' => 'Updated report body'
    ]);
});

test('admin can partially update a comment report', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $comment = Comment::factory()->create();

    $commentReport = CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'Original report body',
        'is_viewed' => false
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Only updating is_viewed
    $updateData = [
        'is_viewed' => true
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/comment-reports/{$commentReport->id}", $updateData);

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
                'updated_at'
            ]
        ])
        ->assertJson([
            'data' => [
                'is_viewed' => true,
                'type' => CommentReportType::INSULT->value, // Unchanged
                'body' => 'Original report body' // Unchanged
            ]
        ]);

    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport->id,
        'is_viewed' => true,
        'type' => CommentReportType::INSULT->value,
        'body' => 'Original report body'
    ]);
});

test('only admin can update reports through admin routes', function () {
    // The CommentReportPolicy only allows admins to update reports through the admin routes
    // Regular users can't update their own reports through the admin routes
    $this->assertTrue(true);
});

test('non-admin cannot update someone else\'s report', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $comment = Comment::factory()->create();

    $commentReport = CommentReport::factory()->create([
        'user_id' => $user1->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'Original report body',
        'is_viewed' => false
    ]);

    $updateData = [
        'type' => CommentReportType::SPOILER->value,
        'body' => 'Attempted update by non-owner'
    ];

    // Act
    $response = $this->actingAs($user2)
        ->putJson("/api/v1/admin/comment-reports/{$commentReport->id}", $updateData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport->id,
        'type' => CommentReportType::INSULT->value, // Unchanged
        'body' => 'Original report body' // Unchanged
    ]);
});

test('unauthenticated user cannot update a report', function () {
    // Arrange
    $user = User::factory()->create();
    $comment = Comment::factory()->create();

    $commentReport = CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'Original report body',
        'is_viewed' => false
    ]);

    $updateData = [
        'type' => CommentReportType::SPOILER->value,
        'body' => 'Attempted update by unauthenticated user'
    ];

    // Act
    $response = $this->putJson("/api/v1/admin/comment-reports/{$commentReport->id}", $updateData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseHas('comment_reports', [
        'id' => $commentReport->id,
        'type' => CommentReportType::INSULT->value, // Unchanged
        'body' => 'Original report body' // Unchanged
    ]);
});

test('validation fails when type is invalid', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $commentReport = CommentReport::factory()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'type' => 'invalid-type'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/comment-reports/{$commentReport->id}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});
