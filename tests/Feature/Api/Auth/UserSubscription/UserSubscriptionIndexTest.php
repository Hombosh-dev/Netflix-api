<?php

namespace Tests\Feature\Api\Auth\UserSubscription;

use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their subscriptions', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();

    // Create subscriptions for the user
    $subscriptions = UserSubscription::factory()->count(3)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);

    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/user-subscriptions');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
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
            ],
            'links',
            'meta'
        ]);

    // Check that we have 3 subscriptions in the response
    $this->assertCount(3, $response->json('data'));
});

test('authenticated user can filter subscriptions by active status', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();

    // Create active subscriptions
    UserSubscription::factory()->count(2)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);

    // Create inactive subscriptions
    UserSubscription::factory()->count(1)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => false
    ]);

    // Act - filter by active status
    $response = $this->actingAs($user)
        ->getJson('/api/v1/user-subscriptions?is_active=1');

    // Assert
    $response->assertStatus(200);

    // Check that we only have active subscriptions in the response
    $subscriptions = $response->json('data');
    foreach ($subscriptions as $subscription) {
        $this->assertTrue($subscription['is_active']);
    }

    // Check that we have 2 active subscriptions
    $this->assertCount(2, $subscriptions);
});

test('authenticated user can filter subscriptions by auto-renew status', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();

    // Create auto-renewable subscriptions
    UserSubscription::factory()->count(2)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'auto_renew' => true
    ]);

    // Create non-auto-renewable subscriptions
    UserSubscription::factory()->count(1)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'auto_renew' => false
    ]);

    // Act - filter by auto-renew status
    $response = $this->actingAs($user)
        ->getJson('/api/v1/user-subscriptions?auto_renew=1');

    // Assert
    $response->assertStatus(200);

    // Check that we only have auto-renewable subscriptions in the response
    $subscriptions = $response->json('data');
    foreach ($subscriptions as $subscription) {
        $this->assertTrue($subscription['auto_renew']);
    }

    // Check that we have 2 auto-renewable subscriptions
    $this->assertCount(2, $subscriptions);
});

test('unauthenticated user cannot view subscriptions', function () {
    // Act
    $response = $this->getJson('/api/v1/user-subscriptions');

    // Assert
    $response->assertStatus(401);
});
