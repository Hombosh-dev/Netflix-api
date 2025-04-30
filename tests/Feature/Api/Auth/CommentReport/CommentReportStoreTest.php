<?php

namespace Tests\Feature\Api\Auth\CommentReport;

use App\Enums\CommentReportType;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can report a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'This comment contains insults and offensive language'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'comment_id',
                'type',
                'type_label',
                'is_viewed',
                'body',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.comment_id', $comment->id)
        ->assertJsonPath('data.type', CommentReportType::INSULT->value)
        ->assertJsonPath('data.body', 'This comment contains insults and offensive language')
        ->assertJsonPath('data.is_viewed', false);
    
    // Check that the report was created in the database
    $this->assertDatabaseHas('comment_reports', [
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value,
        'body' => 'This comment contains insults and offensive language',
        'is_viewed' => false
    ]);
});

test('authenticated user can report a comment without providing a body', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => CommentReportType::SPOILER->value
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.comment_id', $comment->id)
        ->assertJsonPath('data.type', CommentReportType::SPOILER->value)
        ->assertJsonPath('data.body', null);
    
    // Check that the report was created in the database
    $this->assertDatabaseHas('comment_reports', [
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::SPOILER->value
    ]);
});

test('authenticated user cannot report the same comment with the same reason twice', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    // Create an existing report
    CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT
    ]);
    
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
        ->assertJsonPath('message', 'You have already reported this comment for this reason');
    
    // Check that no new report was created
    $this->assertDatabaseCount('comment_reports', 1);
});

test('authenticated user can report the same comment with different reasons', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    // Create an existing report
    CommentReport::factory()->create([
        'user_id' => $user->id,
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT
    ]);
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => CommentReportType::SPOILER->value,
        'body' => 'This comment also contains spoilers'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonPath('data.type', CommentReportType::SPOILER->value);
    
    // Check that a new report was created
    $this->assertDatabaseCount('comment_reports', 2);
});

test('validation fails when required fields are missing', function () {
    // Arrange
    $user = User::factory()->create();
    
    $reportData = [
        // Missing required fields
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
    
    $reportData = [
        'comment_id' => 'non-existent-id',
        'type' => CommentReportType::INSULT->value
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
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => 'invalid-type'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});

test('unauthenticated user cannot report a comment', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    $reportData = [
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT->value
    ];
    
    // Act
    $response = $this->postJson('/api/v1/comment-reports', $reportData);
    
    // Assert
    $response->assertStatus(401);
});
