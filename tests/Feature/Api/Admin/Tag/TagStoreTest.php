<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\User;
use App\Models\Tag;
use App\Actions\Tags\CreateTag;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

uses(RefreshDatabase::class);

test('admin can create a new tag', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tagData = [
        'name' => 'Test Tag',
        'description' => 'This is a test tag description',
        'is_genre' => true,
        'slug' => 'test-tag',
        'meta_title' => 'Test Tag | StreamingService',
        'meta_description' => 'This is a meta description for the test tag',
        'meta_image' => 'https://example.com/images/test-tag.jpg',
        'aliases' => ['test', 'tag', 'example']
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tags', $tagData);

    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_genre',
                'slug',
                'aliases',
                'created_at',
                'updated_at'
            ]
        ]);

    $this->assertDatabaseHas('tags', [
        'name' => 'Test Tag',
        'description' => 'This is a test tag description',
        'is_genre' => true,
        'slug' => 'test-tag',
        'meta_title' => 'Test Tag | StreamingService',
        'meta_description' => 'This is a meta description for the test tag',
    ]);
});

test('admin can create a tag with minimal data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tagData = [
        'name' => 'Minimal Tag',
        'description' => 'This is a minimal tag'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tags', $tagData);

    // Assert
    $response->assertStatus(201);

    $this->assertDatabaseHas('tags', [
        'name' => 'Minimal Tag',
        'description' => 'This is a minimal tag',
    ]);

    // Check that a slug was automatically generated
    $tag = Tag::where('name', 'Minimal Tag')->first();
    $this->assertNotNull($tag->slug);
});

test('non-admin cannot create a tag', function () {
    // Arrange
    $user = User::factory()->create();

    $tagData = [
        'name' => 'Test Tag',
        'description' => 'This is a test tag description'
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/tags', $tagData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseMissing('tags', [
        'name' => 'Test Tag',
        'description' => 'This is a test tag description'
    ]);
});

test('unauthenticated user cannot create a tag', function () {
    // Arrange
    $tagData = [
        'name' => 'Test Tag',
        'description' => 'This is a test tag description'
    ];

    // Act
    $response = $this->postJson('/api/v1/admin/tags', $tagData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseMissing('tags', [
        'name' => 'Test Tag',
        'description' => 'This is a test tag description'
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

    $tagData = [
        // Missing required fields
        'is_genre' => true
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tags', $tagData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description']);
});

test('validation fails when name is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a tag with a specific name
    Tag::factory()->create([
        'name' => 'Existing Tag'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tagData = [
        'name' => 'Existing Tag', // Using an existing name
        'description' => 'This is a test tag description'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tags', $tagData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('validation fails when slug is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a tag with a specific slug
    Tag::factory()->create([
        'slug' => 'existing-slug'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tagData = [
        'name' => 'Test Tag',
        'description' => 'This is a test tag description',
        'slug' => 'existing-slug' // Using an existing slug
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tags', $tagData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('admin can create a tag with JSON string aliases', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tagData = [
        'name' => 'Tag with JSON Aliases ' . uniqid(), // Make name unique
        'description' => 'This is a tag with JSON string aliases',
        'aliases' => json_encode(['json', 'string', 'aliases'])
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tags', $tagData);

    // Assert
    $response->assertStatus(201);

    // Verify that a tag with this description exists in the database
    $this->assertDatabaseHas('tags', [
        'description' => 'This is a tag with JSON string aliases'
    ]);
});
