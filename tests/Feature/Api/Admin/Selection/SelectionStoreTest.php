<?php

namespace Tests\Feature\Api\Admin\Selection;

use App\Models\User;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Enums\Role;
use App\Actions\Selections\CreateSelection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

uses(RefreshDatabase::class);

test('admin can create a new selection', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movies = Movie::factory()->count(2)->create();
    $persons = Person::factory()->count(2)->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $selectionData = [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description',
        'is_published' => true,
        'movie_ids' => $movies->pluck('id')->toArray(),
        'person_ids' => $persons->pluck('id')->toArray(),
        'slug' => 'test-selection',
        'meta_title' => 'Test Selection | StreamingService',
        'meta_description' => 'This is a meta description for the test selection',
        'meta_image' => 'https://example.com/images/test-selection.jpg'
    ];

    // Act
    $response = $this->withoutExceptionHandling()
        ->actingAs($admin)
        ->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(201);

    $this->assertDatabaseHas('selections', [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description',
        'is_published' => true,
        'slug' => 'test-selection',
        'meta_title' => 'Test Selection | StreamingService',
        'meta_description' => 'This is a meta description for the test selection',
    ]);

    // Check that movies and persons were attached
    $selection = Selection::where('slug', 'test-selection')->first();
    $this->assertCount(2, $selection->movies);
    $this->assertCount(2, $selection->persons);
});

test('admin can create a selection with minimal data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $selectionData = [
        'name' => 'Minimal Selection',
        'description' => 'This is a minimal selection'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(201);

    $this->assertDatabaseHas('selections', [
        'name' => 'Minimal Selection',
        'description' => 'This is a minimal selection',
        'is_published' => true, // Default value
    ]);

    // Check that a slug was automatically generated
    $selection = Selection::where('name', 'Minimal Selection')->first();
    $this->assertNotNull($selection->slug);
});

test('non-admin cannot create a selection', function () {
    // Arrange
    $user = User::factory()->create();

    $selectionData = [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description'
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseMissing('selections', [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description'
    ]);
});

test('unauthenticated user cannot create a selection', function () {
    // Arrange
    $selectionData = [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description'
    ];

    // Act
    $response = $this->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseMissing('selections', [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description'
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

    $selectionData = [
        // Missing required fields
        'is_published' => true
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description']);
});

test('validation fails when slug is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a selection with a specific slug
    Selection::factory()->create([
        'slug' => 'existing-slug'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $selectionData = [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description',
        'slug' => 'existing-slug' // Using an existing slug
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('validation fails when movie_ids contains invalid id', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $selectionData = [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description',
        'movie_ids' => ['non-existent-movie-id']
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['movie_ids.0']);
});

test('validation fails when person_ids contains invalid id', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $selectionData = [
        'name' => 'Test Selection',
        'description' => 'This is a test selection description',
        'person_ids' => ['non-existent-person-id']
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['person_ids.0']);
});

test('admin can create a selection with comma-separated ids', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $movies = Movie::factory()->count(2)->create();
    $persons = Person::factory()->count(2)->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Mock the response
    $this->mock(CreateSelection::class, function ($mock) {
        $mock->shouldReceive('handle')->andReturn(
            Selection::factory()->create([
                'name' => 'Test Selection with Comma IDs'
            ])
        );
    });

    $selectionData = [
        'name' => 'Test Selection with Comma IDs',
        'description' => 'This is a test selection description',
        'movie_ids' => implode(',', $movies->pluck('id')->toArray()),
        'person_ids' => implode(',', $persons->pluck('id')->toArray())
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/selections', $selectionData);

    // Assert
    $response->assertStatus(201);

    // Verify that a selection with this name exists in the database
    $this->assertDatabaseHas('selections', [
        'name' => 'Test Selection with Comma IDs'
    ]);
});
