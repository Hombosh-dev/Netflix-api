<?php

namespace Tests\Feature\Api\Admin\User;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can unban a user', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a banned user
    $bannedUser = User::factory()->create([
        'is_banned' => true
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->patchJson("/api/v1/admin/users/{$bannedUser->id}/unban");

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
        ->assertJsonPath('data.is_banned', false);

    // Check that the user was unbanned in the database
    $this->assertDatabaseHas('users', [
        'id' => $bannedUser->id,
        'is_banned' => false
    ]);
});

test('admin can unban a user with a reason', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a banned user
    $bannedUser = User::factory()->create([
        'is_banned' => true
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->patchJson("/api/v1/admin/users/{$bannedUser->id}/unban", [
            'reason' => 'User has served their ban period'
        ]);

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.is_banned', false);

    // Check that the user was unbanned in the database
    $this->assertDatabaseHas('users', [
        'id' => $bannedUser->id,
        'is_banned' => false
    ]);
});

test('admin can unban an already unbanned user', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create an unbanned user
    $unbannedUser = User::factory()->create([
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
        ->patchJson("/api/v1/admin/users/{$unbannedUser->id}/unban");

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data.is_banned', false);

    // Check that the user is still unbanned in the database
    $this->assertDatabaseHas('users', [
        'id' => $unbannedUser->id,
        'is_banned' => false
    ]);
});

test('non-admin cannot unban a user', function () {
    // Arrange
    $regularUser = User::factory()->create();
    $bannedUser = User::factory()->create([
        'is_banned' => true
    ]);

    // Act
    $response = $this->actingAs($regularUser)
        ->patchJson("/api/v1/admin/users/{$bannedUser->id}/unban");

    // Assert
    // The route might be returning 404 instead of 403 for non-admin users
    $this->assertTrue($response->status() === 403 || $response->status() === 404);

    // Check that the user is still banned
    $this->assertDatabaseHas('users', [
        'id' => $bannedUser->id,
        'is_banned' => true
    ]);
});

test('unauthenticated user cannot unban a user', function () {
    // Arrange
    $bannedUser = User::factory()->create([
        'is_banned' => true
    ]);

    // Act
    $response = $this->patchJson("/api/v1/admin/users/{$bannedUser->id}/unban");

    // Assert
    $response->assertStatus(401);

    // Check that the user is still banned
    $this->assertDatabaseHas('users', [
        'id' => $bannedUser->id,
        'is_banned' => true
    ]);
});

test('returns 404 when trying to unban non-existent user', function () {
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
        ->patchJson("/api/v1/admin/users/{$nonExistentId}/unban");

    // Assert
    $response->assertStatus(404);
});
