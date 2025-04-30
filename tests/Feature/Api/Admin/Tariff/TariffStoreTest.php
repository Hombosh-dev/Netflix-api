<?php

namespace Tests\Feature\Api\Tariff;

use App\Models\User;
use App\Models\Tariff;
use App\Enums\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create a new tariff', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    $tariffData = [
        'name' => 'Premium Plan',
        'description' => 'Access to all premium content',
        'price' => 99.99,
        'currency' => 'USD',
        'duration_days' => 30,
        'features' => ['HD Quality', 'No Ads', 'Offline Viewing'],
        'is_active' => true,
        'slug' => 'premium-plan',
        'meta_title' => 'Premium Plan | StreamingService',
        'meta_description' => 'Get access to all premium content with our Premium Plan',
        'meta_image' => 'https://example.com/images/premium-plan.jpg'
    ];

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tariffs', $tariffData);



    // Assert
    $response->assertStatus(201)
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
        ]);

    $this->assertDatabaseHas('tariffs', [
        'name' => 'Premium Plan',
        'description' => 'Access to all premium content',
        'price' => 99.99,
        'currency' => 'USD',
        'duration_days' => 30,
        'is_active' => true,
        'slug' => 'premium-plan',
    ]);
});

test('non-admin cannot create a tariff', function () {
    // Arrange
    $user = User::factory()->create();

    $tariffData = [
        'name' => 'Premium Plan',
        'description' => 'Access to all premium content',
        'price' => 99.99,
        'currency' => 'USD',
        'duration_days' => 30,
        'features' => ['HD Quality', 'No Ads', 'Offline Viewing'],
        'is_active' => true,
        'slug' => 'premium-plan'
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/tariffs', $tariffData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseMissing('tariffs', [
        'name' => 'Premium Plan',
        'slug' => 'premium-plan',
    ]);
});

test('unauthenticated user cannot create a tariff', function () {
    // Arrange
    $tariffData = [
        'name' => 'Premium Plan',
        'description' => 'Access to all premium content',
        'price' => 99.99,
        'currency' => 'USD',
        'duration_days' => 30,
        'features' => ['HD Quality', 'No Ads', 'Offline Viewing'],
        'is_active' => true,
        'slug' => 'premium-plan'
    ];

    // Act
    $response = $this->postJson('/api/v1/admin/tariffs', $tariffData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseMissing('tariffs', [
        'name' => 'Premium Plan',
        'slug' => 'premium-plan',
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

    $tariffData = [
        // Missing required fields
        'is_active' => true
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tariffs', $tariffData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description', 'price', 'currency', 'duration_days', 'features', 'slug']);
});

test('validation fails when slug is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Create a tariff with a specific slug
    Tariff::factory()->create([
        'slug' => 'existing-slug'
    ]);

    $tariffData = [
        'name' => 'Premium Plan',
        'description' => 'Access to all premium content',
        'price' => 99.99,
        'currency' => 'USD',
        'duration_days' => 30,
        'features' => ['HD Quality', 'No Ads', 'Offline Viewing'],
        'is_active' => true,
        'slug' => 'existing-slug' // Using an existing slug
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tariffs', $tariffData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('validation fails when price is negative', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tariffData = [
        'name' => 'Premium Plan',
        'description' => 'Access to all premium content',
        'price' => -10.00, // Negative price
        'currency' => 'USD',
        'duration_days' => 30,
        'features' => ['HD Quality', 'No Ads', 'Offline Viewing'],
        'is_active' => true,
        'slug' => 'premium-plan'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tariffs', $tariffData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['price']);
});

test('validation fails when duration_days is less than 1', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tariffData = [
        'name' => 'Premium Plan',
        'description' => 'Access to all premium content',
        'price' => 99.99,
        'currency' => 'USD',
        'duration_days' => 0, // Invalid duration
        'features' => ['HD Quality', 'No Ads', 'Offline Viewing'],
        'is_active' => true,
        'slug' => 'premium-plan'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/tariffs', $tariffData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['duration_days']);
});
