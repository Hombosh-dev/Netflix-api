<?php

namespace Tests\Feature\Api\Auth\UserSubscription;

use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their active subscriptions', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create active subscriptions for the user
    $activeSubscriptions = UserSubscription::factory()->count(2)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
    
    // Create inactive subscriptions for the user
    $inactiveSubscriptions = UserSubscription::factory()->count(1)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => false
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/user-subscriptions/active');
    
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
    
    // Check that we only have active subscriptions in the response
    $responseData = $response->json('data');
    foreach ($responseData as $subscription) {
        $this->assertTrue($subscription['is_active']);
    }
    
    // Check that we have 2 active subscriptions in the response
    $this->assertCount(2, $responseData);
});

test('authenticated user only sees their own active subscriptions', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create active subscriptions for user1
    $user1Subscriptions = UserSubscription::factory()->count(2)->create([
        'user_id' => $user1->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
    
    // Create active subscriptions for user2
    $user2Subscriptions = UserSubscription::factory()->count(3)->create([
        'user_id' => $user2->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
    
    // Act
    $response = $this->actingAs($user1)
        ->getJson('/api/v1/user-subscriptions/active');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we only have user1's subscriptions in the response
    $responseData = $response->json('data');
    foreach ($responseData as $subscription) {
        $this->assertEquals($user1->id, $subscription['user_id']);
    }
    
    // Check that we have 2 subscriptions in the response (only user1's)
    $this->assertCount(2, $responseData);
});

test('returns empty array when user has no active subscriptions', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create only inactive subscriptions for the user
    $inactiveSubscriptions = UserSubscription::factory()->count(2)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => false
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/user-subscriptions/active');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we have 0 subscriptions in the response
    $this->assertCount(0, $response->json('data'));
});

test('unauthenticated user cannot view active subscriptions', function () {
    // Act
    $response = $this->getJson('/api/v1/user-subscriptions/active');
    
    // Assert
    $response->assertStatus(401);
});
