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

test('admin can create a new episode', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movie = Movie::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $episodeData = [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Test Episode',
        'description' => 'This is a test episode description',
        'duration' => 45,
        'air_date' => '2023-01-01',
        'is_filler' => false,
        'pictures' => json_encode(['https://example.com/images/test-episode.jpg']),
        'video_players' => json_encode([
            [
                'name' => VideoPlayerName::KODIK->value,
                'url' => 'https://example.com/player/123',
                'file_url' => 'https://example.com/video/123.mp4',
                'dubbing' => 'Українська',
                'quality' => VideoQuality::HD->value,
                'locale_code' => 'uk'
            ]
        ]),
        'slug' => 'test-episode',
        'meta_title' => 'Test Episode | StreamingService',
        'meta_description' => 'This is a meta description for the test episode',
        'meta_image' => 'https://example.com/images/test-episode-meta.jpg'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'movie_id',
                'number',
                'name',
                'slug',
                'description',
                'duration',
                'air_date',
                'is_filler',
                'pictures_url',
                'video_players'
            ]
        ]);
    
    $this->assertDatabaseHas('episodes', [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Test Episode',
        'description' => 'This is a test episode description',
        'duration' => 45,
        'is_filler' => false,
        'slug' => 'test-episode',
        'meta_title' => 'Test Episode | StreamingService',
        'meta_description' => 'This is a meta description for the test episode',
        'meta_image' => 'https://example.com/images/test-episode-meta.jpg'
    ]);
});

test('admin can create an episode with minimal data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movie = Movie::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $episodeData = [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Minimal Episode',
        'description' => 'This is a minimal episode'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(201);
    
    $this->assertDatabaseHas('episodes', [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Minimal Episode',
        'description' => 'This is a minimal episode'
    ]);
    
    // Check that a slug was automatically generated
    $episode = Episode::where('name', 'Minimal Episode')->first();
    $this->assertNotNull($episode->slug);
});

test('non-admin cannot create an episode', function () {
    // Arrange
    $user = User::factory()->create();
    $movie = Movie::factory()->create();
    
    $episodeData = [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Test Episode',
        'description' => 'This is a test episode description'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(403);
    
    $this->assertDatabaseMissing('episodes', [
        'movie_id' => $movie->id,
        'name' => 'Test Episode'
    ]);
});

test('unauthenticated user cannot create an episode', function () {
    // Arrange
    $movie = Movie::factory()->create();
    
    $episodeData = [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Test Episode',
        'description' => 'This is a test episode description'
    ];
    
    // Act
    $response = $this->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(401);
    
    $this->assertDatabaseMissing('episodes', [
        'movie_id' => $movie->id,
        'name' => 'Test Episode'
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
    
    $episodeData = [
        // Missing required fields
        'duration' => 45,
        'is_filler' => false
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['movie_id', 'number', 'name', 'description']);
});

test('validation fails when movie_id is invalid', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $episodeData = [
        'movie_id' => 'non-existent-id',
        'number' => 1,
        'name' => 'Test Episode',
        'description' => 'This is a test episode description'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['movie_id']);
});

test('validation fails when slug is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movie = Movie::factory()->create();
    
    // Create an episode with a specific slug
    Episode::factory()->create([
        'slug' => 'existing-slug'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $episodeData = [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Test Episode',
        'description' => 'This is a test episode description',
        'slug' => 'existing-slug' // Using an existing slug
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('validation fails when video_players format is invalid', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movie = Movie::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $episodeData = [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'Test Episode',
        'description' => 'This is a test episode description',
        'video_players' => json_encode([
            [
                // Missing required fields
                'url' => 'https://example.com/player/123'
                // Missing name, quality
            ]
        ])
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['video_players.0.name', 'video_players.0.quality']);
});

test('admin can create an episode with JSON string pictures and video_players', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movie = Movie::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $episodeData = [
        'movie_id' => $movie->id,
        'number' => 1,
        'name' => 'JSON Episode ' . uniqid(), // Make name unique
        'description' => 'This is an episode with JSON string data',
        'pictures' => json_encode(['https://example.com/images/pic1.jpg', 'https://example.com/images/pic2.jpg']),
        'video_players' => json_encode([
            [
                'name' => VideoPlayerName::KODIK->value,
                'url' => 'https://example.com/player/123',
                'quality' => VideoQuality::HD->value
            ]
        ])
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/episodes', $episodeData);
    
    // Assert
    $response->assertStatus(201);
    
    // Verify that an episode with this description exists in the database
    $this->assertDatabaseHas('episodes', [
        'description' => 'This is an episode with JSON string data'
    ]);
    
    // Get the created episode and check that pictures and video_players were properly decoded
    $episode = Episode::where('description', 'This is an episode with JSON string data')->first();
    $this->assertNotNull($episode);
    $this->assertCount(2, $episode->pictures);
    $this->assertCount(1, $episode->video_players);
});
