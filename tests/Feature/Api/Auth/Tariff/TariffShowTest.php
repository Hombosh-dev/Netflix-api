<?php

namespace Tests\Feature\Api\Auth\Tariff;

use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

test('authenticated user can view tariff details', function () {
    // Arrange
    $user = User::factory()->create();

    $features = ['Feature 1', 'Feature 2', 'Feature 3'];
    $tariff = Tariff::factory()->create([
        'name' => 'Premium Tariff',
        'description' => 'This is a premium tariff with many features',
        'price' => 299.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'features' => $features,
        'is_active' => true,
        'slug' => 'premium-tariff'
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/tariffs/{$tariff->slug}");

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
                'meta_title',
                'meta_description',
                'meta_image',
                'user_subscriptions_count',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJsonPath('data.id', $tariff->id)
        ->assertJsonPath('data.name', 'Premium Tariff')
        ->assertJsonPath('data.description', 'This is a premium tariff with many features')
        ->assertJsonPath('data.price', '299.99')
        ->assertJsonPath('data.currency', 'UAH')
        ->assertJsonPath('data.duration_days', 30)
        ->assertJsonPath('data.is_active', true)
        ->assertJsonPath('data.slug', 'premium-tariff');

    // Check that features are included in the response
    $responseFeatures = $response->json('data.features');
    $this->assertIsArray($responseFeatures);
    $this->assertEquals($features, $responseFeatures);
});

test('authenticated user can view inactive tariff details', function () {
    // Arrange
    $user = User::factory()->create();

    $tariff = Tariff::factory()->create([
        'is_active' => false
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/tariffs/{$tariff->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $tariff->id)
        ->assertJsonPath('data.is_active', false);
});

test('admin can view tariff details with subscription count', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    $tariff = Tariff::factory()->create();

    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/tariffs/{$tariff->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $tariff->id)
        ->assertJsonStructure([
            'data' => [
                'user_subscriptions_count'
            ]
        ]);
});

test('unauthenticated user cannot view tariff details', function () {
    // Arrange
    $tariff = Tariff::factory()->create();

    // Act
    $response = $this->getJson("/api/v1/tariffs/{$tariff->slug}");

    // Assert
    $response->assertStatus(401);
});

test('returns 404 when tariff does not exist', function () {
    // Arrange
    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/tariffs/non-existent-slug");

    // Assert
    $response->assertStatus(404);
});
