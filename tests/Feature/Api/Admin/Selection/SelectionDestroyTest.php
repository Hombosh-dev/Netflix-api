<?php

namespace Tests\Feature\Api\Admin\Selection;

use App\Models\User;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\UserList;
use App\Models\Comment;
use App\Enums\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can delete a selection', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a selection with relationships
    $selection = Selection::factory()->create([
        'name' => 'Deletable Selection',
        'slug' => 'deletable-selection'
    ]);

    // Add some movies and persons
    $movies = Movie::factory()->count(2)->create();
    $persons = Person::factory()->count(2)->create();

    $selection->movies()->attach($movies->pluck('id'));
    $selection->persons()->attach($persons->pluck('id'));

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->withoutExceptionHandling()
        ->actingAs($admin)
        ->deleteJson("/api/v1/admin/selections/{$selection->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Selection deleted successfully'
        ]);

    // Check that the selection was deleted
    $this->assertDatabaseMissing('selections', [
        'id' => $selection->id
    ]);
});

test('admin can delete a selection with relationships', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a selection with relationships
    $selection = Selection::factory()->create([
        'name' => 'Selection with Relationships',
        'slug' => 'selection-with-relationships'
    ]);

    // Add some movies and persons
    $movies = Movie::factory()->count(2)->create();
    $persons = Person::factory()->count(2)->create();

    $selection->movies()->attach($movies->pluck('id'));
    $selection->persons()->attach($persons->pluck('id'));

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->withoutExceptionHandling()
        ->actingAs($admin)
        ->deleteJson("/api/v1/admin/selections/{$selection->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Selection deleted successfully'
        ]);

    // Check that the selection was deleted
    $this->assertDatabaseMissing('selections', [
        'id' => $selection->id
    ]);
});

test('non-admin cannot delete a selection', function () {
    // Arrange
    $user = User::factory()->create();

    $selection = Selection::factory()->create([
        'name' => 'Regular Selection',
        'slug' => 'regular-selection'
    ]);

    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/admin/selections/{$selection->slug}");

    // Assert
    $response->assertStatus(403);

    // Check that the selection was not deleted
    $this->assertDatabaseHas('selections', [
        'id' => $selection->id
    ]);
});

test('unauthenticated user cannot delete a selection', function () {
    // Arrange
    $selection = Selection::factory()->create([
        'name' => 'Regular Selection',
        'slug' => 'regular-selection'
    ]);

    // Act
    $response = $this->deleteJson("/api/v1/admin/selections/{$selection->slug}");

    // Assert
    $response->assertStatus(401);

    // Check that the selection was not deleted
    $this->assertDatabaseHas('selections', [
        'id' => $selection->id
    ]);
});

test('returns 404 when trying to delete non-existent selection', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Generate a random ID that doesn't exist
    $nonExistentSlug = 'non-existent-selection';

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/selections/{$nonExistentSlug}");

    // Assert
    $response->assertStatus(404);
});
