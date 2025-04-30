<?php

namespace Tests\Feature\Api\Auth\UserSubscription;

use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their subscription details', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true,
        'auto_renew' => false
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/user-subscriptions/{$subscription->id}");
    
    // Assert
    $response->assertStatus(200)
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
        ->assertJsonPath('data.id', $subscription->id)
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.tariff_id', $tariff->id)
        ->assertJsonPath('data.is_active', true)
        ->assertJsonPath('data.auto_renew', false);
});

test('authenticated user cannot view another user subscription details', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user2->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
    
    // Act
    $response = $this->actingAs($user1)
        ->getJson("/api/v1/user-subscriptions/{$subscription->id}");
    
    // Assert
    $response->assertStatus(403);
});

test('admin can view any user subscription details', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    $subscription = UserSubscription::factory()->create([
        'user_id' => $regularUser->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
    
    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/user-subscriptions/{$subscription->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $subscription->id)
        ->assertJsonPath('data.user_id', $regularUser->id);
});

test('unauthenticated user cannot view subscription details', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    $subscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
    
    // Act
    $response = $this->getJson("/api/v1/user-subscriptions/{$subscription->id}");
    
    // Assert
    $response->assertStatus(401);
});

test('returns 404 when subscription does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/user-subscriptions/non-existent-id");
    
    // Assert
    $response->assertStatus(404);
});
