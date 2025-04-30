<?php

namespace Tests\Feature\Api\Admin\Person;

use App\Models\User;
use App\Models\Person;
use App\Models\Movie;
use App\Enums\PersonType;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can delete a person', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a person
    $person = Person::factory()->create([
        'name' => 'Deletable Person',
        'slug' => 'deletable-person'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/people/{$person->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Person deleted successfully'
        ]);

    // Check that the person was deleted
    $this->assertDatabaseMissing('people', [
        'id' => $person->id
    ]);
});

test('admin cannot delete a person with associated movies', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a person
    $person = Person::factory()->create([
        'name' => 'Person with Movies',
        'slug' => 'person-with-movies',
        'type' => PersonType::ACTOR->value
    ]);

    // Create a movie
    $movie = Movie::factory()->create();

    // Associate the person with the movie
    $movie->persons()->attach($person->id, ['character_name' => 'Test Character']);

    // Verify the association was created
    $this->assertDatabaseHas('movie_person', [
        'movie_id' => $movie->id,
        'person_id' => $person->id,
        'character_name' => 'Test Character'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/people/{$person->slug}");

    // Assert
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Cannot delete person with associated movies. Remove associations first.'
        ]);

    // Check that the person was not deleted
    $this->assertDatabaseHas('people', [
        'id' => $person->id
    ]);
});

test('non-admin cannot delete a person', function () {
    // Arrange
    $user = User::factory()->create();

    $person = Person::factory()->create([
        'name' => 'Regular Person',
        'slug' => 'regular-person'
    ]);

    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/admin/people/{$person->slug}");

    // Assert
    $response->assertStatus(403);

    // Check that the person was not deleted
    $this->assertDatabaseHas('people', [
        'id' => $person->id
    ]);
});

test('unauthenticated user cannot delete a person', function () {
    // Arrange
    $person = Person::factory()->create([
        'name' => 'Regular Person',
        'slug' => 'regular-person'
    ]);

    // Act
    $response = $this->deleteJson("/api/v1/admin/people/{$person->slug}");

    // Assert
    $response->assertStatus(401);

    // Check that the person was not deleted
    $this->assertDatabaseHas('people', [
        'id' => $person->id
    ]);
});

test('returns 404 when trying to delete non-existent person', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Generate a non-existent slug
    $nonExistentSlug = 'non-existent-person';

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/people/{$nonExistentSlug}");

    // Assert
    $response->assertStatus(404);
});
