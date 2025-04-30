<?php

namespace Tests\Feature\Api\Admin\Movie;

use App\Models\User;
use App\Models\Movie;
use App\Models\Episode;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can delete a movie', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a movie
    $movie = Movie::factory()->create([
        'name' => 'Movie to Delete',
        'slug' => 'movie-to-delete',
        'image_name' => 'https://example.com/images/movie-to-delete.jpg'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/movies/{$movie->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Movie deleted successfully'
        ]);

    // Check that the movie was deleted
    $this->assertDatabaseMissing('movies', [
        'id' => $movie->id
    ]);
});

test('admin cannot delete a movie with episodes', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a movie
    $movie = Movie::factory()->create([
        'name' => 'Movie with Episodes',
        'slug' => 'movie-with-episodes',
        'image_name' => 'https://example.com/images/movie-with-episodes.jpg'
    ]);

    // Create an episode for the movie
    $episode = Episode::factory()->create([
        'movie_id' => $movie->id,
        'name' => 'Test Episode',
        'number' => 1
    ]);

    // Verify the episode was created
    $this->assertDatabaseHas('episodes', [
        'movie_id' => $movie->id,
        'name' => 'Test Episode'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/movies/{$movie->slug}");

    // Assert
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Cannot delete movie with episodes. Delete episodes first.'
        ]);

    // Check that the movie was not deleted
    $this->assertDatabaseHas('movies', [
        'id' => $movie->id,
        'name' => 'Movie with Episodes'
    ]);
});

test('non-admin cannot delete a movie', function () {
    // Arrange
    $user = User::factory()->create(); // Regular user, not admin

    $movie = Movie::factory()->create([
        'name' => 'Regular Movie',
        'slug' => 'regular-movie',
        'image_name' => 'https://example.com/images/regular-movie.jpg'
    ]);

    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/admin/movies/{$movie->slug}");

    // Assert
    $response->assertStatus(403); // Forbidden

    // Check that the movie was not deleted
    $this->assertDatabaseHas('movies', [
        'id' => $movie->id,
        'name' => 'Regular Movie'
    ]);
});

test('unauthenticated user cannot delete a movie', function () {
    // Arrange
    $movie = Movie::factory()->create([
        'name' => 'Regular Movie',
        'slug' => 'regular-movie',
        'image_name' => 'https://example.com/images/regular-movie.jpg'
    ]);

    // Act
    $response = $this->deleteJson("/api/v1/admin/movies/{$movie->slug}");

    // Assert
    $response->assertStatus(401);

    // Check that the movie was not deleted
    $this->assertDatabaseHas('movies', [
        'id' => $movie->id,
        'image_name' => 'https://example.com/images/regular-movie.jpg'
    ]);
});

test('returns 404 when trying to delete non-existent movie', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Generate a non-existent slug
    $nonExistentSlug = 'non-existent-movie';

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/movies/{$nonExistentSlug}");

    // Assert
    $response->assertStatus(404);
});
