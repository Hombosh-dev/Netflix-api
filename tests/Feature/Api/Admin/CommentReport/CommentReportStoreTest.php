<?php

namespace Tests\Feature\Api\CommentReport;

use App\Models\User;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Enums\CommentReportType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create a comment report', function () {
    // Arrange
    $user = User::factory()->create();
    $comment = Comment::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        return true;
    });
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'This comment contains insulting content'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(201)
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
        ]);
    
    $this->assertDatabaseHas('comment_reports', [
        'comment_id' => $comment->id,
        'user_id' => $user->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'This comment contains insulting content',
        'is_viewed' => false
    ]);
});

test('unauthenticated user cannot create a comment report', function () {
    // Arrange
    $comment = Comment::factory()->create();
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'This comment contains insulting content'
    ];
    
    // Act
    $response = $this->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(401);
    
    $this->assertDatabaseMissing('comment_reports', [
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
    ]);
});

test('user cannot report the same comment with the same reason twice', function () {
    // Arrange
    $user = User::factory()->create();
    $comment = Comment::factory()->create();
    
    // Create an existing report
    CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        return true;
    });
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'This is a duplicate report'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'You have already reported this comment for this reason'
        ]);
    
    // Verify only one report exists
    $this->assertDatabaseCount('comment_reports', 1);
});

test('user can report the same comment with different reasons', function () {
    // Arrange
    $user = User::factory()->create();
    $comment = Comment::factory()->create();
    
    // Create an existing report
    CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        return true;
    });
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => CommentReportType::SPOILER->value, // Different reason
        'body' => 'This comment contains spoilers'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(201);
    
    // Verify two reports exist
    $this->assertDatabaseCount('comment_reports', 2);
    
    $this->assertDatabaseHas('comment_reports', [
        'comment_id' => $comment->id,
        'user_id' => $user->id,
        'type' => CommentReportType::SPOILER->value,
        'body' => 'This comment contains spoilers'
    ]);
});

test('validation fails when required fields are missing', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        return true;
    });
    
    $reportData = [
        // Missing required fields
        'body' => 'This is a report without required fields'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['comment_id', 'type']);
});

test('validation fails when comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        return true;
    });
    
    $reportData = [
        'comment_id' => 'non-existent-comment-id',
        'type' => CommentReportType::INSULT->value,
        'body' => 'This comment does not exist'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['comment_id']);
});

test('validation fails when type is invalid', function () {
    // Arrange
    $user = User::factory()->create();
    $comment = Comment::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        return true;
    });
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => 'invalid-type',
        'body' => 'This report has an invalid type'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});
