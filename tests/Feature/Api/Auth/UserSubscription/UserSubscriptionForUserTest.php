<?php

namespace Tests\Feature\Api\Auth\UserSubscription;

use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their own subscriptions', function () {
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
        ->getJson("/api/v1/user-subscriptions/user/{$user->id}");
    
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
    
    // Check that all subscriptions belong to the user
    $responseData = $response->json('data');
    foreach ($responseData as $subscription) {
        $this->assertEquals($user->id, $subscription['user_id']);
    }
});

test('authenticated user cannot view another user subscriptions', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create subscriptions for user2
    $subscriptions = UserSubscription::factory()->count(3)->create([
        'user_id' => $user2->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
    
    // Act
    $response = $this->actingAs($user1)
        ->getJson("/api/v1/user-subscriptions/user/{$user2->id}");
    
    // Assert
    $response->assertStatus(403);
});

test('admin can view any user subscriptions', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create subscriptions for the regular user
    $subscriptions = UserSubscription::factory()->count(3)->create([
        'user_id' => $regularUser->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
    
    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/user-subscriptions/user/{$regularUser->id}");
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we have 3 subscriptions in the response
    $this->assertCount(3, $response->json('data'));
});

test('unauthenticated user cannot view user subscriptions', function () {
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
    $response = $this->getJson("/api/v1/user-subscriptions/user/{$user->id}");
    
    // Assert
    $response->assertStatus(401);
});

test('returns 404 when user does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/user-subscriptions/user/non-existent-id");
    
    // Assert
    $response->assertStatus(404);
});
