<?php

namespace Tests\Feature\Api\Auth\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their own payments', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create payments for the user
    $payments = Payment::factory()->count(3)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'status' => PaymentStatus::SUCCESS
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/payments/user/{$user->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'tariff_id',
                    'amount',
                    'currency',
                    'payment_method',
                    'transaction_id',
                    'status',
                    'status_label',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links',
            'meta'
        ]);
    
    // Check that we have 3 payments in the response
    $this->assertCount(3, $response->json('data'));
    
    // Check that all payments belong to the user
    $responseData = $response->json('data');
    foreach ($responseData as $payment) {
        $this->assertEquals($user->id, $payment['user_id']);
    }
});

test('authenticated user cannot view another user payments', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create payments for user2
    $payments = Payment::factory()->count(3)->create([
        'user_id' => $user2->id,
        'tariff_id' => $tariff->id,
        'status' => PaymentStatus::SUCCESS
    ]);
    
    // Act
    $response = $this->actingAs($user1)
        ->getJson("/api/v1/payments/user/{$user2->id}");
    
    // Assert
    $response->assertStatus(403);
});

test('admin can view any user payments', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $regularUser = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create payments for the regular user
    $payments = Payment::factory()->count(3)->create([
        'user_id' => $regularUser->id,
        'tariff_id' => $tariff->id,
        'status' => PaymentStatus::SUCCESS
    ]);
    
    // Act
    $response = $this->actingAs($admin)
        ->getJson("/api/v1/payments/user/{$regularUser->id}");
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we have 3 payments in the response
    $this->assertCount(3, $response->json('data'));
});

test('unauthenticated user cannot view user payments', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create payments for the user
    $payments = Payment::factory()->count(3)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'status' => PaymentStatus::SUCCESS
    ]);
    
    // Act
    $response = $this->getJson("/api/v1/payments/user/{$user->id}");
    
    // Assert
    $response->assertStatus(401);
});

test('returns 404 when user does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/payments/user/non-existent-id");
    
    // Assert
    $response->assertStatus(404);
});
