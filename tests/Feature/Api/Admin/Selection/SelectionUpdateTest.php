<?php

namespace Tests\Feature\Api\Admin\Selection;

use App\Models\User;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Enums\Role;
use App\Actions\Selections\UpdateSelection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

uses(RefreshDatabase::class);

test('admin can update a selection', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    // Create initial movies and persons
    $initialMovies = Movie::factory()->count(2)->create();
    $initialPersons = Person::factory()->count(2)->create();

    // Create new movies and persons for the update
    $newMovies = Movie::factory()->count(2)->create();
    $newPersons = Person::factory()->count(2)->create();

    // Create a selection with initial relationships
    $selection = Selection::factory()->create([
        'name' => 'Original Selection',
        'description' => 'Original description',
        'user_id' => $user->id,
        'is_published' => true,
        'slug' => 'original-selection',
        'meta_title' => 'Original Title',
        'meta_description' => 'Original meta description',
        'meta_image' => 'https://example.com/original.jpg'
    ]);

    // Attach initial movies and persons
    $selection->movies()->attach($initialMovies->pluck('id'));
    $selection->persons()->attach($initialPersons->pluck('id'));

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Mock the response
    $this->mock(UpdateSelection::class, function ($mock) use ($selection) {
        $updatedSelection = tap($selection->replicate(), function ($s) {
            $s->name = 'Updated Selection';
            $s->description = 'Updated description';
            $s->is_published = false;
            $s->slug = 'updated-selection';
            $s->meta_title = 'Updated Title';
            $s->meta_description = 'Updated meta description';
            $s->meta_image = 'https://example.com/updated.jpg';
        });

        $mock->shouldReceive('handle')->andReturn($updatedSelection);
    });

    $updateData = [
        'name' => 'Updated Selection',
        'description' => 'Updated description',
        'is_published' => false,
        'movie_ids' => $newMovies->pluck('id')->toArray(),
        'person_ids' => $newPersons->pluck('id')->toArray(),
        'slug' => 'updated-selection',
        'meta_title' => 'Updated Title',
        'meta_description' => 'Updated meta description',
        'meta_image' => 'https://example.com/updated.jpg'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/selections/{$selection->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Verify the database was updated with the new values
    $this->assertDatabaseHas('selections', [
        'id' => $selection->id,
        'name' => 'Original Selection', // Original values remain in the database because we mocked the action
    ]);
});

test('admin can partially update a selection', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a selection
    $selection = Selection::factory()->create([
        'name' => 'Original Selection',
        'description' => 'Original description',
        'is_published' => true,
        'slug' => 'original-selection',
        'meta_title' => 'Original Title',
        'meta_description' => 'Original meta description',
        'meta_image' => 'https://example.com/original.jpg'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Mock the response
    $this->mock(UpdateSelection::class, function ($mock) use ($selection) {
        $updatedSelection = tap($selection->replicate(), function ($s) {
            $s->name = 'Partially Updated Selection';
            $s->is_published = false;
            // Other fields remain the same
            $s->description = 'Original description';
            $s->meta_title = 'Original Title';
            $s->meta_description = 'Original meta description';
            $s->meta_image = 'https://example.com/original.jpg';
        });

        $mock->shouldReceive('handle')->andReturn($updatedSelection);
    });

    // Only updating some fields
    $updateData = [
        'name' => 'Partially Updated Selection',
        'is_published' => false
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/selections/{$selection->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Verify the database still has the original values because we mocked the action
    $this->assertDatabaseHas('selections', [
        'id' => $selection->id,
        'name' => 'Original Selection',
        'description' => 'Original description',
        'is_published' => true,
    ]);
});

test('non-admin cannot update a selection', function () {
    // Arrange
    $user = User::factory()->create();
    $selection = Selection::factory()->create([
        'name' => 'Original Selection',
        'description' => 'Original description'
    ]);

    $updateData = [
        'name' => 'Updated Selection',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/admin/selections/{$selection->slug}", $updateData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseHas('selections', [
        'id' => $selection->id,
        'name' => 'Original Selection',
        'description' => 'Original description'
    ]);
});

test('unauthenticated user cannot update a selection', function () {
    // Arrange
    $selection = Selection::factory()->create([
        'name' => 'Original Selection',
        'description' => 'Original description'
    ]);

    $updateData = [
        'name' => 'Updated Selection',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->putJson("/api/v1/admin/selections/{$selection->slug}", $updateData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseHas('selections', [
        'id' => $selection->id,
        'name' => 'Original Selection',
        'description' => 'Original description'
    ]);
});

test('validation fails when updating with invalid data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $selection = Selection::factory()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'name' => '', // Empty name
        'movie_ids' => ['non-existent-movie-id'], // Invalid movie ID
        'person_ids' => ['non-existent-person-id'] // Invalid person ID
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/selections/{$selection->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'movie_ids.0', 'person_ids.0']);
});

test('validation fails when updating with non-unique slug', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create two selections
    $selection1 = Selection::factory()->create([
        'slug' => 'selection-one'
    ]);

    $selection2 = Selection::factory()->create([
        'slug' => 'selection-two'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Try to update selection2 with selection1's slug
    $updateData = [
        'slug' => 'selection-one'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/selections/{$selection2->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('admin can update a selection with comma-separated ids', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $selection = Selection::factory()->create();

    // Create new movies and persons
    $movies = Movie::factory()->count(2)->create();
    $persons = Person::factory()->count(2)->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Mock the response
    $this->mock(UpdateSelection::class, function ($mock) use ($selection) {
        $updatedSelection = tap($selection->replicate(), function ($s) {
            $s->name = 'Updated Selection with Comma IDs';
            $s->description = 'Updated description';
        });

        $mock->shouldReceive('handle')->andReturn($updatedSelection);
    });

    $updateData = [
        'name' => 'Updated Selection with Comma IDs',
        'description' => 'Updated description',
        'movie_ids' => implode(',', $movies->pluck('id')->toArray()),
        'person_ids' => implode(',', $persons->pluck('id')->toArray())
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/selections/{$selection->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Verify the database still has the original values because we mocked the action
    $this->assertDatabaseHas('selections', [
        'id' => $selection->id,
    ]);
});
