<?php

namespace Tests\Feature\Api\Auth\UserSubscription;

use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create a subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true
    ]);

    $subscriptionData = [
        'tariff_id' => $tariff->id,
        'start_date' => now()->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(30)->format('Y-m-d H:i:s'),
        'is_active' => true,
        'auto_renew' => false
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/user-subscriptions', $subscriptionData);

    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'tariff_id',
                'start_date',
                'end_date',
                'is_active',
                'auto_renew',
                'days_left',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.tariff_id', $tariff->id)
        ->assertJsonPath('data.is_active', true)
        ->assertJsonPath('data.auto_renew', false);

    // Check that the subscription was created in the database
    $this->assertDatabaseHas('user_subscriptions', [
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true,
        'auto_renew' => false
    ]);
});

test('end date is calculated based on tariff duration if not provided', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true
    ]);

    $startDate = now();
    $subscriptionData = [
        'tariff_id' => $tariff->id,
        'start_date' => $startDate->format('Y-m-d H:i:s'),
        'is_active' => true,
        'auto_renew' => false
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/user-subscriptions', $subscriptionData);

    // Assert
    $response->assertStatus(201);

    // Get the created subscription
    $subscriptionId = $response->json('data.id');
    $subscription = UserSubscription::find($subscriptionId);

    // Check that the end date is calculated correctly (start date + 30 days)
    $expectedEndDate = $startDate->copy()->addDays(30);
    $this->assertTrue($subscription->end_date->isSameDay($expectedEndDate));
});

test('cannot create subscription if user already has an active one', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'duration_days' => 30,
        'is_active' => true
    ]);

    // Create an existing active subscription
    UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);

    $subscriptionData = [
        'tariff_id' => $tariff->id,
        'start_date' => now()->format('Y-m-d H:i:s'),
        'end_date' => now()->addDays(30)->format('Y-m-d H:i:s'),
        'is_active' => true
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/user-subscriptions', $subscriptionData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonPath('message', 'User already has an active subscription');
});

test('validation fails when required fields are missing', function () {
    // Arrange
    $user = User::factory()->create();

    $subscriptionData = [
        // Missing required fields
    ];

    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/user-subscriptions', $subscriptionData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['tariff_id']);
});

test('unauthenticated user cannot create a subscription', function () {
    // Arrange
    $tariff = Tariff::factory()->create();

    $subscriptionData = [
        'tariff_id' => $tariff->id,
        'is_active' => true
    ];

    // Act
    $response = $this->postJson('/api/v1/user-subscriptions', $subscriptionData);

    // Assert
    $response->assertStatus(401);
});
