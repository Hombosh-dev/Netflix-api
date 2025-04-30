<?php

namespace Tests\Feature\Api\Stats;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view subscription stats', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some subscriptions for stats
    $user = User::factory()->create();
    UserSubscription::factory()->count(3)->create([
        'user_id' => $user->id,
        'is_active' => true
    ]);
    UserSubscription::factory()->count(2)->create([
        'user_id' => $user->id,
        'is_active' => false
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/stats/subscriptions');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'total',
                'active',
                'expired',
                'new_today',
                'new_this_week',
                'new_this_month',
                'new_this_period'
            ]
        ]);
});

test('admin can view subscription stats with custom days parameter', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some subscriptions for stats
    $user = User::factory()->create();
    UserSubscription::factory()->count(3)->create([
        'user_id' => $user->id
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/stats/subscriptions?days=30');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'total',
                'active',
                'expired',
                'new_today',
                'new_this_week',
                'new_this_month',
                'new_this_period'
            ]
        ]);
});

test('non-admin cannot view subscription stats', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'user']);

    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/admin/stats/subscriptions');

    // Assert
    $response->assertStatus(403);
});

test('unauthenticated user cannot view subscription stats', function () {
    // Act
    $response = $this->getJson('/api/v1/admin/stats/subscriptions');

    // Assert
    $response->assertStatus(401);
});
