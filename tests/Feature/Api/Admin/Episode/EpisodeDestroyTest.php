<?php

namespace Tests\Feature\Api\Admin\Episode;

use App\Models\User;
use App\Models\Episode;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can delete an episode', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create an episode
    $episode = Episode::factory()->create([
        'name' => 'Deletable Episode',
        'slug' => 'deletable-episode'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/episodes/{$episode->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Episode deleted successfully'
        ]);

    // Check that the episode was deleted
    $this->assertDatabaseMissing('episodes', [
        'id' => $episode->id
    ]);
});

test('admin cannot delete an episode with comments', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    // Create an episode
    $episode = Episode::factory()->create([
        'name' => 'Episode with Comments',
        'slug' => 'episode-with-comments'
    ]);

    // Create a comment for the episode
    $comment = Comment::factory()->create([
        'commentable_type' => Episode::class,
        'commentable_id' => $episode->id,
        'user_id' => $user->id,
        'body' => 'Test comment'
    ]);

    // Verify the comment was created
    $this->assertDatabaseHas('comments', [
        'commentable_type' => Episode::class,
        'commentable_id' => $episode->id
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/episodes/{$episode->slug}");

    // Assert
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Cannot delete episode with comments. Delete comments first.'
        ]);

    // Check that the episode was not deleted
    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id
    ]);
});

test('non-admin cannot delete an episode', function () {
    // Arrange
    $user = User::factory()->create();

    $episode = Episode::factory()->create([
        'name' => 'Regular Episode',
        'slug' => 'regular-episode'
    ]);

    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/admin/episodes/{$episode->slug}");

    // Assert
    $response->assertStatus(403);

    // Check that the episode was not deleted
    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id
    ]);
});

test('unauthenticated user cannot delete an episode', function () {
    // Arrange
    $episode = Episode::factory()->create([
        'name' => 'Regular Episode',
        'slug' => 'regular-episode'
    ]);

    // Act
    $response = $this->deleteJson("/api/v1/admin/episodes/{$episode->slug}");

    // Assert
    $response->assertStatus(401);

    // Check that the episode was not deleted
    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id
    ]);
});

test('returns 404 when trying to delete non-existent episode', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Generate a non-existent slug
    $nonExistentSlug = 'non-existent-episode';

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/episodes/{$nonExistentSlug}");

    // Assert
    $response->assertStatus(404);
});
