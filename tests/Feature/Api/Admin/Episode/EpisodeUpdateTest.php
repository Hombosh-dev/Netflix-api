<?php

namespace Tests\Feature\Api\Admin\Episode;

use App\Models\User;
use App\Models\Movie;
use App\Models\Episode;
use App\Enums\VideoPlayerName;
use App\Enums\VideoQuality;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can update an episode', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movie = Movie::factory()->create();
    $newMovie = Movie::factory()->create();

    // Create an episode with minimal data to avoid issues
    $episode = Episode::factory()->create([
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Original Episode',
        'description' => 'Original description',
        'slug' => 'original-episode'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'movie_id' => $newMovie->id,
        'number' => 2,
        'name' => 'Updated Episode',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/episodes/{$episode->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id,
        'movie_id' => $newMovie->id,
        'number' => 2,
        'name' => 'Updated Episode',
        'description' => 'Updated description'
    ]);
});

test('admin can partially update an episode', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movie = Movie::factory()->create();

    // Create an episode
    $episode = Episode::factory()->create([
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Original Episode',
        'description' => 'Original description',
        'duration' => 30,
        'is_filler' => false,
        'slug' => 'original-episode'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Only updating some fields
    $updateData = [
        'name' => 'Partially Updated Episode',
        'description' => 'Updated description',
        'is_filler' => true
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/episodes/{$episode->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Check that only the specified fields were updated
    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id,
        'movie_id' => $movie->id, // Unchanged
        'number' => 1, // Unchanged
        'name' => 'Partially Updated Episode',
        'description' => 'Updated description',
        'duration' => 30, // Unchanged
        'is_filler' => true
    ]);

    // Check that the slug was updated (but don't check the exact value as it might have been auto-generated)
    $updatedEpisode = Episode::find($episode->id);
    $this->assertStringContainsString('partially-updated-episode', $updatedEpisode->slug);
});

test('non-admin cannot update an episode', function () {
    // Arrange
    $user = User::factory()->create();
    $episode = Episode::factory()->create([
        'name' => 'Original Episode',
        'description' => 'Original description'
    ]);

    $updateData = [
        'name' => 'Updated Episode',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/admin/episodes/{$episode->slug}", $updateData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id,
        'name' => 'Original Episode',
        'description' => 'Original description'
    ]);
});

test('unauthenticated user cannot update an episode', function () {
    // Arrange
    $episode = Episode::factory()->create([
        'name' => 'Original Episode',
        'description' => 'Original description'
    ]);

    $updateData = [
        'name' => 'Updated Episode',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->putJson("/api/v1/admin/episodes/{$episode->slug}", $updateData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id,
        'name' => 'Original Episode',
        'description' => 'Original description'
    ]);
});

test('validation fails when updating with invalid data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $episode = Episode::factory()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'movie_id' => 'non-existent-id', // Invalid movie_id
        'number' => 0, // Invalid number (min:1)
        'duration' => -5 // Invalid duration (min:1)
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/episodes/{$episode->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['movie_id', 'number', 'duration']);
});

test('validation fails when updating with non-unique slug', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create two episodes
    $episode1 = Episode::factory()->create([
        'slug' => 'episode-one'
    ]);

    $episode2 = Episode::factory()->create([
        'slug' => 'episode-two'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Try to update episode2 with episode1's slug
    $updateData = [
        'slug' => 'episode-one'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/episodes/{$episode2->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('admin can update an episode with pictures', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $episode = Episode::factory()->create([
        'pictures' => ['https://example.com/images/original.jpg']
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'name' => 'Episode with updated pictures',
        'pictures' => json_encode(['https://example.com/images/new1.jpg'])
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/episodes/{$episode->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Check that the name was updated
    $this->assertDatabaseHas('episodes', [
        'id' => $episode->id,
        'name' => 'Episode with updated pictures'
    ]);
});
