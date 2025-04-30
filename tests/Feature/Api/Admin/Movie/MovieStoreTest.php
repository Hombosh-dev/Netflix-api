<?php

namespace Tests\Feature\Api\Admin\Movie;

use App\Models\User;
use App\Models\Movie;
use App\Models\Studio;
use App\Enums\Kind;
use App\Enums\Status;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create a new movie', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $studio = Studio::factory()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Mock the CreateMovie action to return a movie
    $this->mock(\App\Actions\Movies\CreateMovie::class, function ($mock) {
        $movie = Movie::factory()->make([
            'name' => 'Test Movie',
            'description' => 'This is a test movie description',
            'kind' => Kind::MOVIE->value,
            'status' => Status::RELEASED->value,
            'slug' => 'test-movie-unique',
            'image_name' => 'https://example.com/images/test-movie.jpg'
        ]);

        $mock->shouldReceive('handle')->andReturn($movie);
    });

    $movieData = [
        'name' => 'Test Movie',
        'description' => 'This is a test movie description',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value,
        'studio_id' => $studio->id,
        'poster' => 'https://example.com/images/test-movie-poster.jpg',
        'countries' => json_encode(['UA', 'US']),
        'aliases' => json_encode(['Test', 'Movie']),
        'first_air_date' => '2023-01-01',
        'duration' => 120,
        'imdb_score' => 8.5,
        'is_published' => true,
        'slug' => 'test-movie-unique',
        'image_name' => 'https://example.com/images/test-movie.jpg',
        'meta_title' => 'Test Movie | StreamingService',
        'meta_description' => 'This is a meta description for the test movie',
        'meta_image' => 'https://example.com/images/test-movie-meta.jpg'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(200);
});

test('admin can create a movie with minimal data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Mock the CreateMovie action to return a movie
    $this->mock(\App\Actions\Movies\CreateMovie::class, function ($mock) {
        $movie = Movie::factory()->make([
            'name' => 'Minimal Movie',
            'description' => 'This is a minimal movie',
            'kind' => Kind::MOVIE->value,
            'status' => Status::RELEASED->value,
            'image_name' => 'https://example.com/images/minimal-movie.jpg'
        ]);

        $mock->shouldReceive('handle')->andReturn($movie);
    });

    $movieData = [
        'name' => 'Minimal Movie',
        'description' => 'This is a minimal movie',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value,
        'image_name' => 'https://example.com/images/minimal-movie.jpg'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(200);
});

test('non-admin cannot create a movie', function () {
    // Arrange
    $user = User::factory()->create();

    $movieData = [
        'name' => 'Test Movie',
        'description' => 'This is a test movie description',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseMissing('movies', [
        'name' => 'Test Movie',
        'description' => 'This is a test movie description'
    ]);
});

test('unauthenticated user cannot create a movie', function () {
    // Arrange
    $movieData = [
        'name' => 'Test Movie',
        'description' => 'This is a test movie description',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value
    ];

    // Act
    $response = $this->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseMissing('movies', [
        'name' => 'Test Movie',
        'description' => 'This is a test movie description'
    ]);
});

test('validation fails when required fields are missing', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $movieData = [
        // Missing required fields
        'poster' => 'https://example.com/images/test-movie-poster.jpg'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description', 'kind', 'status']);
});

test('validation fails when slug is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a movie with a specific slug
    Movie::factory()->create([
        'slug' => 'existing-slug'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $movieData = [
        'name' => 'Test Movie',
        'description' => 'This is a test movie description',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value,
        'slug' => 'existing-slug' // Using an existing slug
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('admin can create a movie with JSON string arrays', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $uniqueName = 'Movie with JSON Arrays ' . uniqid();

    // Mock the CreateMovie action to return a movie
    $this->mock(\App\Actions\Movies\CreateMovie::class, function ($mock) use ($uniqueName) {
        $movie = Movie::factory()->make([
            'name' => $uniqueName,
            'description' => 'This is a movie with JSON string arrays',
            'kind' => Kind::MOVIE->value,
            'status' => Status::RELEASED->value,
            'image_name' => 'https://example.com/images/json-arrays-movie.jpg',
            'countries' => ['UA', 'US'],
            'aliases' => ['Test', 'Movie', 'JSON']
        ]);

        $mock->shouldReceive('handle')->andReturn($movie);
    });

    $movieData = [
        'name' => $uniqueName, // Make name unique
        'description' => 'This is a movie with JSON string arrays',
        'kind' => Kind::MOVIE->value,
        'status' => Status::RELEASED->value,
        'image_name' => 'https://example.com/images/json-arrays-movie.jpg',
        'countries' => json_encode(['UA', 'US']),
        'aliases' => json_encode(['Test', 'Movie', 'JSON']),
        'attachments' => json_encode([
            [
                'type' => 'trailer',
                'url' => 'https://example.com/trailer',
                'title' => 'Official Trailer',
                'duration' => 120
            ]
        ])
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/movies', $movieData);

    // Assert
    $response->assertStatus(200);
});
