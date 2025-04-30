<?php

namespace Tests\Feature\Api\Admin\Person;

use App\Models\User;
use App\Models\Person;
use App\Enums\PersonType;
use App\Enums\Gender;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can update a person', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a person
    $person = Person::factory()->create([
        'name' => 'Original Person',
        'type' => PersonType::ACTOR->value,
        'original_name' => 'Оригінальна Особа',
        'gender' => Gender::MALE->value,
        'image' => 'https://example.com/original.jpg',
        'description' => 'Original description',
        'birthday' => '1990-01-01',
        'birthplace' => 'Original City',
        'slug' => 'original-person',
        'meta_title' => 'Original Title',
        'meta_description' => 'Original meta description',
        'meta_image' => 'https://example.com/original-meta.jpg'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'name' => 'Updated Person',
        'type' => PersonType::DIRECTOR->value,
        'original_name' => 'Оновлена Особа',
        'gender' => Gender::FEMALE->value,
        'image' => 'https://example.com/updated.jpg',
        'description' => 'Updated description',
        'birthday' => '1995-05-05',
        'birthplace' => 'Updated City',
        'slug' => 'updated-person',
        'meta_title' => 'Updated Title',
        'meta_description' => 'Updated meta description',
        'meta_image' => 'https://example.com/updated-meta.jpg'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/people/{$person->slug}", $updateData);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'image'
            ]
        ]);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Updated Person',
        'type' => PersonType::DIRECTOR->value,
        'original_name' => 'Оновлена Особа',
        'gender' => Gender::FEMALE->value,
        'image' => 'https://example.com/updated.jpg',
        'description' => 'Updated description',
        'birthplace' => 'Updated City',
        'slug' => 'updated-person',
        'meta_title' => 'Updated Title',
        'meta_description' => 'Updated meta description',
        'meta_image' => 'https://example.com/updated-meta.jpg'
    ]);
});

test('admin can partially update a person', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a person
    $person = Person::factory()->create([
        'name' => 'Original Person',
        'type' => PersonType::ACTOR->value,
        'description' => 'Original description',
        'slug' => 'original-person'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Only updating some fields
    $updateData = [
        'name' => 'Partially Updated Person',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/people/{$person->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Check that only the specified fields were updated
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Partially Updated Person',
        'description' => 'Updated description',
        'type' => PersonType::ACTOR->value // Unchanged
    ]);

    // Check that the slug was updated (but don't check the exact value as it might have been auto-generated)
    $updatedPerson = Person::find($person->id);
    $this->assertStringContainsString('partially-updated-person', $updatedPerson->slug);
});

test('non-admin cannot update a person', function () {
    // Arrange
    $user = User::factory()->create();
    $person = Person::factory()->create([
        'name' => 'Original Person',
        'type' => PersonType::ACTOR->value,
        'description' => 'Original description'
    ]);

    $updateData = [
        'name' => 'Updated Person',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/admin/people/{$person->slug}", $updateData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Original Person',
        'description' => 'Original description'
    ]);
});

test('unauthenticated user cannot update a person', function () {
    // Arrange
    $person = Person::factory()->create([
        'name' => 'Original Person',
        'type' => PersonType::ACTOR->value,
        'description' => 'Original description'
    ]);

    $updateData = [
        'name' => 'Updated Person',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->putJson("/api/v1/admin/people/{$person->slug}", $updateData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'name' => 'Original Person',
        'description' => 'Original description'
    ]);
});

test('validation fails when updating with invalid data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $person = Person::factory()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'name' => '', // Empty name
        'type' => 'invalid-type', // Invalid type
        'birthday' => now()->addYear()->format('Y-m-d') // Future date
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/people/{$person->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'type', 'birthday']);
});

test('validation fails when updating with non-unique slug', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create two people
    $person1 = Person::factory()->create([
        'slug' => 'person-one'
    ]);

    $person2 = Person::factory()->create([
        'slug' => 'person-two'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Try to update person2 with person1's slug
    $updateData = [
        'slug' => 'person-one'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/people/{$person2->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('admin can update a person with other gender', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a person with gender
    $person = Person::factory()->create([
        'name' => 'Original Person',
        'gender' => Gender::MALE->value
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Update with other gender
    $updateData = [
        'gender' => Gender::OTHER->value
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/people/{$person->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Check that gender was updated
    $this->assertDatabaseHas('people', [
        'id' => $person->id,
        'gender' => Gender::OTHER->value
    ]);
});
