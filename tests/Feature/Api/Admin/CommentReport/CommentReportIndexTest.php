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

test('admin can view all comment reports', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create multiple reports
    CommentReport::factory()->count(5)->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/comment-reports');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'comment_id',
                    'user_id',
                    'type',
                    'body',
                    'is_viewed',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links',
            'meta'
        ]);

    // Check that we have 5 reports in the response
    $this->assertCount(5, $response->json('data'));
});

test('admin can filter reports by comment_id', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $comment1 = Comment::factory()->create();
    $comment2 = Comment::factory()->create();

    // Create reports for different comments
    CommentReport::factory()->count(3)->create(['comment_id' => $comment1->id]);
    CommentReport::factory()->count(2)->create(['comment_id' => $comment2->id]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports?comment_id={$comment1->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we only have reports for comment1
    $this->assertCount(3, $response->json('data'));
    foreach ($response->json('data') as $report) {
        $this->assertEquals($comment1->id, $report['comment_id']);
    }
});

test('admin can filter reports by user_id', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create reports from different users
    CommentReport::factory()->count(3)->create(['user_id' => $user1->id]);
    CommentReport::factory()->count(2)->create(['user_id' => $user2->id]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports?user_id={$user1->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we only have reports from user1
    $this->assertCount(3, $response->json('data'));
    foreach ($response->json('data') as $report) {
        $this->assertEquals($user1->id, $report['user_id']);
    }
});

test('admin can filter reports by type', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create reports with different types
    CommentReport::factory()->count(3)->create(['type' => CommentReportType::INSULT]);
    CommentReport::factory()->count(2)->create(['type' => CommentReportType::SPOILER]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports?type=" . CommentReportType::INSULT->value);

    // Assert
    $response->assertStatus(200);

    // Check that we only have reports of type INSULT
    $this->assertCount(3, $response->json('data'));
    foreach ($response->json('data') as $report) {
        $this->assertEquals(CommentReportType::INSULT->value, $report['type']);
    }
});

test('admin can filter reports by is_viewed status', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create viewed and unviewed reports
    CommentReport::factory()->count(3)->create(['is_viewed' => true]);
    CommentReport::factory()->count(2)->create(['is_viewed' => false]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports?is_viewed=0");

    // Assert
    $response->assertStatus(200);

    // Check that we only have unviewed reports
    $this->assertCount(2, $response->json('data'));
    foreach ($response->json('data') as $report) {
        $this->assertFalse($report['is_viewed']);
    }
});

test('admin can get unviewed reports via dedicated endpoint', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create viewed and unviewed reports
    CommentReport::factory()->count(3)->create(['is_viewed' => true]);
    CommentReport::factory()->count(2)->create(['is_viewed' => false]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports/unviewed");

    // Assert
    $response->assertStatus(200);

    // Check that we only have unviewed reports
    $this->assertCount(2, $response->json('data'));
    foreach ($response->json('data') as $report) {
        $this->assertFalse($report['is_viewed']);
    }
});

test('admin can sort reports by created_at', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create reports at different times
    CommentReport::factory()->create(['created_at' => now()->subDays(3)]);
    CommentReport::factory()->create(['created_at' => now()->subDays(2)]);
    CommentReport::factory()->create(['created_at' => now()->subDays(1)]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act - sort ascending
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports?sort=created_at&direction=asc");

    // Assert
    $response->assertStatus(200);

    // Check that reports are sorted by created_at in ascending order
    $reports = $response->json('data');
    $this->assertCount(3, $reports);

    $createdAtValues = array_column($reports, 'created_at');
    $sortedCreatedAtValues = $createdAtValues;
    sort($sortedCreatedAtValues);

    $this->assertEquals($sortedCreatedAtValues, $createdAtValues);
});

test('non-admin cannot view all reports', function () {
    // Arrange
    $user = User::factory()->create();

    // Create reports
    CommentReport::factory()->count(3)->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/admin/comment-reports');

    // Assert
    $response->assertStatus(403);
});

test('unauthenticated user cannot view reports', function () {
    // Arrange
    CommentReport::factory()->count(3)->create();

    // Act
    $response = $this->getJson('/api/v1/admin/comment-reports');

    // Assert
    $response->assertStatus(401);
});

test('admin can get reports for a specific comment', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $comment = Comment::factory()->create();

    // Create reports for this comment
    CommentReport::factory()->count(3)->create(['comment_id' => $comment->id]);

    // Create reports for other comments
    CommentReport::factory()->count(2)->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/comment-reports?comment_id={$comment->id}");

    // Assert
    $response->assertStatus(200);

    // Check that we only have reports for the specific comment
    $reports = $response->json('data');
    foreach ($reports as $report) {
        $this->assertEquals($comment->id, $report['comment_id']);
    }
});
