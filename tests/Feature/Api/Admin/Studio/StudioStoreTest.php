<?php

namespace Tests\Feature\Api\Admin\Studio;

use App\Models\User;
use App\Models\Studio;
use App\Actions\Studios\CreateStudio;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create a new studio', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $studioData = [
        'name' => 'Test Studio',
        'description' => 'This is a test studio description',
        'image' => 'https://example.com/images/test-studio.jpg',
        'slug' => 'test-studio',
        'meta_title' => 'Test Studio | StreamingService',
        'meta_description' => 'This is a meta description for the test studio',
        'meta_image' => 'https://example.com/images/test-studio-meta.jpg',
        'aliases' => ['test', 'studio', 'example']
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/studios', $studioData);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'image',
                'slug',
                'aliases',
                'created_at',
                'updated_at'
            ]
        ]);
    
    $this->assertDatabaseHas('studios', [
        'name' => 'Test Studio',
        'description' => 'This is a test studio description',
        'image' => 'https://example.com/images/test-studio.jpg',
        'slug' => 'test-studio',
        'meta_title' => 'Test Studio | StreamingService',
        'meta_description' => 'This is a meta description for the test studio',
        'meta_image' => 'https://example.com/images/test-studio-meta.jpg',
    ]);
});

test('admin can create a studio with minimal data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $studioData = [
        'name' => 'Minimal Studio',
        'description' => 'This is a minimal studio'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/studios', $studioData);
    
    // Assert
    $response->assertStatus(201);
    
    $this->assertDatabaseHas('studios', [
        'name' => 'Minimal Studio',
        'description' => 'This is a minimal studio',
    ]);
    
    // Check that a slug was automatically generated
    $studio = Studio::where('name', 'Minimal Studio')->first();
    $this->assertNotNull($studio->slug);
    
    // Check that a meta title was automatically generated
    $this->assertNotNull($studio->meta_title);
    $this->assertStringContainsString('Minimal Studio', $studio->meta_title);
});

test('non-admin cannot create a studio', function () {
    // Arrange
    $user = User::factory()->create();
    
    $studioData = [
        'name' => 'Test Studio',
        'description' => 'This is a test studio description'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/studios', $studioData);
    
    // Assert
    $response->assertStatus(403);
    
    $this->assertDatabaseMissing('studios', [
        'name' => 'Test Studio',
        'description' => 'This is a test studio description'
    ]);
});

test('unauthenticated user cannot create a studio', function () {
    // Arrange
    $studioData = [
        'name' => 'Test Studio',
        'description' => 'This is a test studio description'
    ];
    
    // Act
    $response = $this->postJson('/api/v1/admin/studios', $studioData);
    
    // Assert
    $response->assertStatus(401);
    
    $this->assertDatabaseMissing('studios', [
        'name' => 'Test Studio',
        'description' => 'This is a test studio description'
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
    
    $studioData = [
        // Missing required fields
        'image' => 'https://example.com/images/test-studio.jpg'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/studios', $studioData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description']);
});

test('validation fails when name is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a studio with a specific name
    Studio::factory()->create([
        'name' => 'Existing Studio'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $studioData = [
        'name' => 'Existing Studio', // Using an existing name
        'description' => 'This is a test studio description'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/studios', $studioData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('validation fails when slug is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a studio with a specific slug
    Studio::factory()->create([
        'slug' => 'existing-slug'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $studioData = [
        'name' => 'Test Studio',
        'description' => 'This is a test studio description',
        'slug' => 'existing-slug' // Using an existing slug
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/studios', $studioData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('admin can create a studio with JSON string aliases', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $studioData = [
        'name' => 'Studio with JSON Aliases ' . uniqid(), // Make name unique
        'description' => 'This is a studio with JSON string aliases',
        'aliases' => json_encode(['json', 'string', 'aliases'])
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/studios', $studioData);
    
    // Assert
    $response->assertStatus(201);
    
    // Verify that a studio with this description exists in the database
    $this->assertDatabaseHas('studios', [
        'description' => 'This is a studio with JSON string aliases'
    ]);
    
    // Get the created studio and check that aliases were properly decoded
    $studio = Studio::where('description', 'This is a studio with JSON string aliases')->first();
    $this->assertNotNull($studio);
    $this->assertCount(3, $studio->aliases);
    $this->assertTrue($studio->aliases->contains('json'));
    $this->assertTrue($studio->aliases->contains('string'));
    $this->assertTrue($studio->aliases->contains('aliases'));
});
