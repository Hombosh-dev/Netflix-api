<?php

namespace Tests\Feature\Api\Stats;

use App\Models\User;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view payment stats', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some payments for stats
    $user = User::factory()->create();
    Payment::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => PaymentStatus::SUCCESS
    ]);
    Payment::factory()->count(1)->create([
        'user_id' => $user->id,
        'status' => PaymentStatus::PENDING
    ]);
    Payment::factory()->count(1)->create([
        'user_id' => $user->id,
        'status' => PaymentStatus::FAILED
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/stats/payments');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'total_payments',
                'successful_payments',
                'pending_payments',
                'failed_payments',
                'total_amount',
                'today_amount',
                'this_week_amount',
                'this_month_amount',
                'this_period_amount'
            ]
        ]);
});

test('admin can view payment stats with custom days parameter', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some payments for stats
    $user = User::factory()->create();
    Payment::factory()->count(3)->create([
        'user_id' => $user->id,
        'status' => PaymentStatus::SUCCESS
    ]);

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/stats/payments?days=30');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'total_payments',
                'successful_payments',
                'pending_payments',
                'failed_payments',
                'total_amount',
                'today_amount',
                'this_week_amount',
                'this_month_amount',
                'this_period_amount'
            ]
        ]);
});

test('non-admin cannot view payment stats', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'user']);

    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/admin/stats/payments');

    // Assert
    $response->assertStatus(403);
});

test('unauthenticated user cannot view payment stats', function () {
    // Act
    $response = $this->getJson('/api/v1/admin/stats/payments');

    // Assert
    $response->assertStatus(401);
});
