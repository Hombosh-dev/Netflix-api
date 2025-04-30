<?php

namespace Tests\Feature\Api\Stats;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view user stats', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some users for stats
    User::factory()->count(5)->create();

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/stats/users');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'total',
                'new_today',
                'new_this_week',
                'new_this_month',
                'active_users',
                'banned_users',
            ]
        ]);
});

test('admin can view user stats with custom days parameter', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some users for stats
    User::factory()->count(5)->create();

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/stats/users?days=30');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'total',
                'new_today',
                'new_this_week',
                'new_this_month',
                'active_users',
                'banned_users',
            ]
        ]);
});

test('non-admin cannot view user stats', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'user']);

    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/admin/stats/users');

    // Assert
    $response->assertStatus(403);
});

test('unauthenticated user cannot view user stats', function () {
    // Act
    $response = $this->getJson('/api/v1/admin/stats/users');

    // Assert
    $response->assertStatus(401);
});
