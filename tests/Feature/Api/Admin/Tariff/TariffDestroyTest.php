<?php

namespace Tests\Feature\Api\Tariff;

use App\Models\User;
use App\Models\Tariff;
use App\Models\UserSubscription;
use App\Enums\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can delete a tariff without active subscriptions', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $tariff = Tariff::factory()->create([
        'name' => 'Deletable Plan',
        'slug' => 'deletable-plan'
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/tariffs/{$tariff->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Tariff deleted successfully'
        ]);

    $this->assertDatabaseMissing('tariffs', [
        'id' => $tariff->id
    ]);
});

test('admin cannot delete a tariff with active subscriptions', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    $user = User::factory()->create();

    $tariff = Tariff::factory()->create([
        'name' => 'Active Plan',
        'slug' => 'active-plan'
    ]);

    // Create an active subscription for this tariff
    UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true,
        'start_date' => now(),
        'end_date' => now()->addDays(30)
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/tariffs/{$tariff->slug}");

    // Assert
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Cannot delete tariff with active subscriptions'
        ]);

    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id
    ]);
});

test('admin can delete a tariff with inactive subscriptions', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    $user = User::factory()->create();

    $tariff = Tariff::factory()->create([
        'name' => 'Inactive Plan',
        'slug' => 'inactive-plan'
    ]);

    // Create an inactive subscription for this tariff
    UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => false,
        'start_date' => now()->subDays(60),
        'end_date' => now()->subDays(30)
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/tariffs/{$tariff->slug}");

    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Tariff deleted successfully'
        ]);

    $this->assertDatabaseMissing('tariffs', [
        'id' => $tariff->id
    ]);
});

test('non-admin cannot delete a tariff', function () {
    // Arrange
    $user = User::factory()->create();

    $tariff = Tariff::factory()->create([
        'name' => 'Regular Plan',
        'slug' => 'regular-plan'
    ]);

    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/admin/tariffs/{$tariff->slug}");

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id
    ]);
});

test('unauthenticated user cannot delete a tariff', function () {
    // Arrange
    $tariff = Tariff::factory()->create([
        'name' => 'Regular Plan',
        'slug' => 'regular-plan'
    ]);

    // Act
    $response = $this->deleteJson("/api/v1/admin/tariffs/{$tariff->slug}");

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseHas('tariffs', [
        'id' => $tariff->id
    ]);
});

test('returns 404 when trying to delete non-existent tariff', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Generate a random ID that doesn't exist
    $nonExistentId = 'non-existent-id';

    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/tariffs/{$nonExistentId}");

    // Assert
    $response->assertStatus(404);
});
