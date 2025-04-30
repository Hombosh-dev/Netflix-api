<?php

namespace Tests\Feature\Api\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can ban a user', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a user to ban
    $userToBan = User::factory()->create([
        'is_banned' => false
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->patchJson("/api/v1/admin/users/{$userToBan->id}/ban", [
            'reason' => 'Violation of terms of service'
        ]);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'is_banned'
            ]
        ])
        ->assertJsonPath('data.is_banned', true);

    // Check that the user was banned in the database
    $this->assertDatabaseHas('users', [
        'id' => $userToBan->id,
        'is_banned' => true
    ]);
});

test('admin can ban a user without providing a reason', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a user to ban
    $userToBan = User::factory()->create([
        'is_banned' => false
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->patchJson("/api/v1/admin/users/{$userToBan->id}/ban");

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.is_banned', true);

    // Check that the user was banned in the database
    $this->assertDatabaseHas('users', [
        'id' => $userToBan->id,
        'is_banned' => true
    ]);
});

test('admin cannot ban another admin', function () {
    // Arrange
    $admin1 = User::factory()->admin()->create();
    $admin2 = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin1)
        ->patchJson("/api/v1/admin/users/{$admin2->id}/ban");

    // Assert
    $response->assertStatus(403)
        ->assertJson([
            'message' => 'Cannot ban an admin user'
        ]);

    // Check that the admin was not banned
    $this->assertDatabaseHas('users', [
        'id' => $admin2->id,
        'is_banned' => false
    ]);
});

test('admin cannot ban themselves', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->patchJson("/api/v1/admin/users/{$admin->id}/ban");

    // Assert
    $response->assertStatus(403)
        ->assertJson([
            'message' => 'Cannot ban yourself'
        ]);

    // Check that the admin was not banned
    $this->assertDatabaseHas('users', [
        'id' => $admin->id,
        'is_banned' => false
    ]);
});

test('non-admin cannot ban a user', function () {
    // Arrange
    $regularUser = User::factory()->create();
    $userToBan = User::factory()->create([
        'is_banned' => false
    ]);

    // Act
    $response = $this->actingAs($regularUser)
        ->patchJson("/api/v1/admin/users/{$userToBan->id}/ban");

    // Assert
    $response->assertStatus(403);

    // Check that the user was not banned
    $this->assertDatabaseHas('users', [
        'id' => $userToBan->id,
        'is_banned' => false
    ]);
});

test('unauthenticated user cannot ban a user', function () {
    // Arrange
    $userToBan = User::factory()->create([
        'is_banned' => false
    ]);

    // Act
    $response = $this->patchJson("/api/v1/admin/users/{$userToBan->id}/ban");

    // Assert
    $response->assertStatus(401);

    // Check that the user was not banned
    $this->assertDatabaseHas('users', [
        'id' => $userToBan->id,
        'is_banned' => false
    ]);
});

test('returns 404 when trying to ban non-existent user', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Generate a non-existent ID
    $nonExistentId = 'non-existent-id';

    // Act
    $response = $this->actingAs($admin)
        ->patchJson("/api/v1/admin/users/{$nonExistentId}/ban");

    // Assert
    $response->assertStatus(404);
});
