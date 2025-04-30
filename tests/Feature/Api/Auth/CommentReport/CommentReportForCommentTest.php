<?php

namespace Tests\Feature\Api\Auth\CommentReport;

use App\Enums\CommentReportType;
use App\Models\Comment;
use App\Models\CommentReport;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view reports for a comment', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    // Create reports for the comment
    $reports = CommentReport::factory()->count(3)->create([
        'comment_id' => $comment->id
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-reports/comment/{$comment->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
            'links',
            'meta'
        ]);
    
    // Check that we have 3 reports in the response
    $this->assertCount(3, $response->json('data'));
    
    // Check that all reports belong to the comment
    $responseData = $response->json('data');
    foreach ($responseData as $report) {
        $this->assertEquals($comment->id, $report['comment_id']);
    }
});

test('authenticated user can filter reports by type', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    // Create reports with different types
    CommentReport::factory()->count(2)->create([
        'comment_id' => $comment->id,
        'type' => CommentReportType::INSULT
    ]);
    
    CommentReport::factory()->count(1)->create([
        'comment_id' => $comment->id,
        'type' => CommentReportType::SPOILER
    ]);
    
    // Act - filter by type
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-reports/comment/{$comment->id}?type=" . CommentReportType::INSULT->value);
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we only have reports with the specified type in the response
    $reports = $response->json('data');
    foreach ($reports as $report) {
        $this->assertEquals(CommentReportType::INSULT->value, $report['type']);
    }
    
    // Check that we have 2 reports with the specified type
    $this->assertCount(2, $reports);
});

test('authenticated user can filter reports by viewed status', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    // Create viewed reports
    CommentReport::factory()->count(2)->create([
        'comment_id' => $comment->id,
        'is_viewed' => true
    ]);
    
    // Create unviewed reports
    CommentReport::factory()->count(1)->create([
        'comment_id' => $comment->id,
        'is_viewed' => false
    ]);
    
    // Act - filter by viewed status
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-reports/comment/{$comment->id}?is_viewed=1");
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we only have viewed reports in the response
    $reports = $response->json('data');
    foreach ($reports as $report) {
        $this->assertTrue($report['is_viewed']);
    }
    
    // Check that we have 2 viewed reports
    $this->assertCount(2, $reports);
});

test('authenticated user can sort reports', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    // Create reports with different creation dates
    CommentReport::factory()->create([
        'comment_id' => $comment->id,
        'created_at' => now()->subDays(2)
    ]);
    
    CommentReport::factory()->create([
        'comment_id' => $comment->id,
        'created_at' => now()->subDays(1)
    ]);
    
    CommentReport::factory()->create([
        'comment_id' => $comment->id,
        'created_at' => now()
    ]);
    
    // Act - sort by creation date ascending
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-reports/comment/{$comment->id}?sort=created_at&direction=asc");
    
    // Assert
    $response->assertStatus(200);
    
    // Check that reports are sorted by creation date in ascending order
    $reports = $response->json('data');
    $creationDates = array_column($reports, 'created_at');
    $sortedDates = $creationDates;
    sort($sortedDates);
    
    $this->assertEquals($sortedDates, $creationDates);
});

test('returns empty array when comment has no reports', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-reports/comment/{$comment->id}");
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we have 0 reports in the response
    $this->assertCount(0, $response->json('data'));
});

test('returns 404 when comment does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/comment-reports/comment/non-existent-id");
    
    // Assert
    $response->assertStatus(404);
});

test('unauthenticated user cannot view reports for a comment', function () {
    // Arrange
    $movie = Movie::factory()->create();
    $comment = Comment::factory()->create([
        'commentable_id' => $movie->id,
        'commentable_type' => Movie::class,
    ]);
    
    // Create reports for the comment
    $reports = CommentReport::factory()->count(3)->create([
        'comment_id' => $comment->id
    ]);
    
    // Act
    $response = $this->getJson("/api/v1/comment-reports/comment/{$comment->id}");
    
    // Assert
    $response->assertStatus(401);
});
