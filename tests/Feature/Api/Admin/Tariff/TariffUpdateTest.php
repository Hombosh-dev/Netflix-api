<?php

namespace Tests\Feature\Api\Tariff;

use App\Models\User;
use App\Models\Tariff;
use App\Enums\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can update a tariff', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tariff = Tariff::factory()->create([
        'name' => 'Original Plan',
        'description' => 'Original description',
        'price' => 79.99,
        'currency' => 'USD',
        'duration_days' => 30,
        'features' => ['Original Feature 1', 'Original Feature 2'],
        'is_active' => true,
        'slug' => 'original-plan'
    ]);

    $updateData = [
        'name' => 'Updated Plan',
        'description' => 'Updated description',
        'price' => 89.99,
        'currency' => 'EUR',
        'duration_days' => 60,
        'features' => ['Updated Feature 1', 'Updated Feature 2', 'New Feature'],
        'is_active' => false,
        'slug' => 'updated-plan'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tariffs/{$tariff->slug}", $updateData);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'currency',
                'duration_days',
                'features',
                'is_active',
                'slug',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJson([
            'data' => [
                'name' => 'Updated Plan',
                'description' => 'Updated description',
                'price' => '89.99',
                'currency' => 'EUR',
                'duration_days' => 60,
                'is_active' => false,
                'slug' => 'updated-plan'
            ]
        ]);

    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id,
        'name' => 'Updated Plan',
        'description' => 'Updated description',
        'price' => 89.99,
        'currency' => 'EUR',
        'duration_days' => 60,
        'is_active' => false,
        'slug' => 'updated-plan'
    ]);
});

test('admin can partially update a tariff', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tariff = Tariff::factory()->create([
        'name' => 'Original Plan',
        'description' => 'Original description',
        'price' => 79.99,
        'currency' => 'USD',
        'duration_days' => 30,
        'features' => ['Original Feature 1', 'Original Feature 2'],
        'is_active' => true,
        'slug' => 'original-plan'
    ]);

    // Only updating some fields
    $updateData = [
        'name' => 'Partially Updated Plan',
        'price' => 99.99
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tariffs/{$tariff->slug}", $updateData);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'currency',
                'duration_days',
                'features',
                'is_active',
                'slug',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJson([
            'data' => [
                'name' => 'Partially Updated Plan',
                'price' => '99.99',
                // These fields should remain unchanged
                'description' => 'Original description',
                'currency' => 'USD',
                'duration_days' => 30,
                'is_active' => true,
                'slug' => 'original-plan'
            ]
        ]);

    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id,
        'name' => 'Partially Updated Plan',
        'price' => 99.99,
        // These fields should remain unchanged
        'description' => 'Original description',
        'currency' => 'USD',
        'duration_days' => 30,
        'is_active' => true,
        'slug' => 'original-plan'
    ]);
});

test('non-admin cannot update a tariff', function () {
    // Arrange
    $user = User::factory()->create();

    $tariff = Tariff::factory()->create([
        'name' => 'Original Plan',
        'price' => 79.99
    ]);

    $updateData = [
        'name' => 'Updated Plan',
        'price' => 89.99
    ];

    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/admin/tariffs/{$tariff->slug}", $updateData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id,
        'name' => 'Original Plan',
        'price' => 79.99
    ]);
});

test('unauthenticated user cannot update a tariff', function () {
    // Arrange
    $tariff = Tariff::factory()->create([
        'name' => 'Original Plan',
        'price' => 79.99
    ]);

    $updateData = [
        'name' => 'Updated Plan',
        'price' => 89.99
    ];

    // Act
    $response = $this->putJson("/api/v1/admin/tariffs/{$tariff->slug}", $updateData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id,
        'name' => 'Original Plan',
        'price' => 79.99
    ]);
});

test('validation fails when updating with invalid data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tariff = Tariff::factory()->create();

    $updateData = [
        'price' => -10.00, // Negative price
        'duration_days' => 0, // Invalid duration
        'currency' => 'INVALID' // Invalid currency (should be 3 characters)
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tariffs/{$tariff->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['price', 'duration_days', 'currency']);
});

test('validation fails when updating with non-unique slug', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Create two tariffs
    $tariff1 = Tariff::factory()->create([
        'slug' => 'tariff-one'
    ]);

    $tariff2 = Tariff::factory()->create([
        'slug' => 'tariff-two'
    ]);

    // Try to update tariff2 with tariff1's slug
    $updateData = [
        'slug' => 'tariff-one'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tariffs/{$tariff2->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});
