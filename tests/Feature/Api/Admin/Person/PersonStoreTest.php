<?php

namespace Tests\Feature\Api\Admin\Person;

use App\Models\User;
use App\Models\Person;
use App\Enums\PersonType;
use App\Enums\Gender;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create a new person', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $personData = [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value,
        'original_name' => 'Тест Персон',
        'gender' => Gender::MALE->value,
        'image' => 'https://example.com/images/test-person.jpg',
        'description' => 'This is a test person description',
        'birthday' => '1990-01-01',
        'birthplace' => 'Test City',
        'slug' => 'test-person',
        'meta_title' => 'Test Person | StreamingService',
        'meta_description' => 'This is a meta description for the test person',
        'meta_image' => 'https://example.com/images/test-person-meta.jpg'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'image'
            ]
        ]);
    
    $this->assertDatabaseHas('people', [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value,
        'original_name' => 'Тест Персон',
        'gender' => Gender::MALE->value,
        'image' => 'https://example.com/images/test-person.jpg',
        'description' => 'This is a test person description',
        'birthplace' => 'Test City',
        'slug' => 'test-person',
        'meta_title' => 'Test Person | StreamingService',
        'meta_description' => 'This is a meta description for the test person',
        'meta_image' => 'https://example.com/images/test-person-meta.jpg'
    ]);
});

test('admin can create a person with minimal data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $personData = [
        'name' => 'Minimal Person',
        'type' => PersonType::DIRECTOR->value
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(201);
    
    $this->assertDatabaseHas('people', [
        'name' => 'Minimal Person',
        'type' => PersonType::DIRECTOR->value
    ]);
    
    // Check that a slug was automatically generated
    $person = Person::where('name', 'Minimal Person')->first();
    $this->assertNotNull($person->slug);
});

test('non-admin cannot create a person', function () {
    // Arrange
    $user = User::factory()->create();
    
    $personData = [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(403);
    
    $this->assertDatabaseMissing('people', [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value
    ]);
});

test('unauthenticated user cannot create a person', function () {
    // Arrange
    $personData = [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value
    ];
    
    // Act
    $response = $this->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(401);
    
    $this->assertDatabaseMissing('people', [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value
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
    
    $personData = [
        // Missing required fields
        'description' => 'This is a test person description'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'type']);
});

test('validation fails when slug is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a person with a specific slug
    Person::factory()->create([
        'slug' => 'existing-slug'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $personData = [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value,
        'slug' => 'existing-slug' // Using an existing slug
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('validation fails when birthday is in the future', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $personData = [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value,
        'birthday' => now()->addYear()->format('Y-m-d') // Future date
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['birthday']);
});

test('validation fails when gender is invalid', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $personData = [
        'name' => 'Test Person',
        'type' => PersonType::ACTOR->value,
        'gender' => 'invalid-gender' // Invalid gender
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['gender']);
});

test('validation fails when type is invalid', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $personData = [
        'name' => 'Test Person',
        'type' => 'invalid-type' // Invalid type
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/people', $personData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type']);
});
