<?php

use App\Enums\Kind;
use App\Enums\Status;
use App\Models\Movie;
use App\Models\Studio;
use App\Models\User;
use App\Models\Episode;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create a new movie', function () {
    $this->markTestSkipped('This test requires more complex setup with file uploads.');
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);
    $studio = Studio::factory()->create();

    $movieData = [
        'name' => 'New Test Movie',
        'description' => 'This is a new test movie',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value,
        'studio_id' => $studio->id,
        'imdb_score' => 8.5,
        'duration' => 120,
        'is_published' => true,
        'poster' => 'movies/poster.jpg',
        'image_name' => 'poster.jpg'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(201)
        ->assertJsonPath('name', 'New Test Movie')
        ->assertJsonPath('description', 'This is a new test movie')
        ->assertJsonPath('kind', Kind::MOVIE->value)
        ->assertJsonPath('status', Status::RELEASED->value)
        ->assertJsonPath('imdb_score', 8.5)
        ->assertJsonPath('duration', 120);

    // Check that the movie was created in the database
    $this->assertDatabaseHas('movies', [
        'name' => 'New Test Movie',
        'description' => 'This is a new test movie',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value,
        'studio_id' => $studio->id,
        'imdb_score' => 8.5,
        'duration' => 120,
        'is_published' => true
    ]);
});

test('non-admin cannot create a movie', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'user']);
    $studio = Studio::factory()->create();

    $movieData = [
        'name' => 'New Test Movie',
        'description' => 'This is a new test movie',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value,
        'studio_id' => $studio->id,
        'imdb_score' => 8.5,
        'duration' => 120,
        'is_published' => true,
        'poster' => 'movies/poster.jpg',
        'image_name' => 'poster.jpg'
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(403);

    // Check that the movie was not created in the database
    $this->assertDatabaseMissing('movies', [
        'name' => 'New Test Movie'
    ]);
});

test('admin can update a movie', function () {
    $this->markTestSkipped('This test requires more complex setup with file uploads.');
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Original Movie Name',
        'description' => 'Original description',
        'is_published' => true,
        'image_name' => 'poster.jpg'
    ]);

    $updateData = [
        'name' => 'Updated Movie Name',
        'description' => 'Updated description',
        'imdb_score' => 9.0
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/movies/{$movie->slug}", $updateData);

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('name', 'Updated Movie Name')
        ->assertJsonPath('description', 'Updated description')
        ->assertJsonPath('imdb_score', 9.0);

    // Check that the movie was updated in the database
    $this->assertDatabaseHas('movies', [
        'id' => $movie->id,
        'name' => 'Updated Movie Name',
        'description' => 'Updated description',
        'imdb_score' => 9.0
    ]);
});

test('admin can delete a movie without episodes', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Movie to Delete',
        'is_published' => true,
        'image_name' => 'poster.jpg'
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/movies/{$movie->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('message', 'Movie deleted successfully');

    // Check that the movie was deleted from the database
    $this->assertDatabaseMissing('movies', [
        'id' => $movie->id
    ]);
});

test('admin cannot delete a movie with episodes', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Movie with Episodes',
        'is_published' => true,
        'image_name' => 'poster.jpg'
    ]);

    // Create an episode for the movie
    Episode::factory()->create([
        'movie_id' => $movie->id,
        'name' => 'Test Episode'
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/movies/{$movie->slug}");

    // Assert
    $response->assertStatus(422)
        ->assertJsonPath('message', 'Cannot delete movie with episodes. Delete episodes first.');

    // Check that the movie was not deleted from the database
    $this->assertDatabaseHas('movies', [
        'id' => $movie->id
    ]);
});
