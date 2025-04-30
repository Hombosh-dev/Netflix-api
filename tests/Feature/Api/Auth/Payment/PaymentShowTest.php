<?php

namespace Tests\Feature\Api\Auth\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their payment details', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'status' => PaymentStatus::SUCCESS
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/payments/{$payment->id}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
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
        ])
        ->assertJsonPath('data.id', $payment->id)
        ->assertJsonPath('data.amount', (string)$payment->amount)
        ->assertJsonPath('data.currency', $payment->currency)
        ->assertJsonPath('data.payment_method', $payment->payment_method)
        ->assertJsonPath('data.status', $payment->status->value);
});

test('authenticated user cannot view another user payment details', function () {
    // Arrange
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    $payment = Payment::factory()->create([
        'user_id' => $user2->id,
        'tariff_id' => $tariff->id,
        'status' => PaymentStatus::SUCCESS
    ]);
    
    // Act
    $response = $this->actingAs($user1)
        ->getJson("/api/v1/payments/{$payment->id}");
    
    // Assert
    $response->assertStatus(403);
});

test('unauthenticated user cannot view payment details', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'status' => PaymentStatus::SUCCESS
    ]);
    
    // Act
    $response = $this->getJson("/api/v1/payments/{$payment->id}");
    
    // Assert
    $response->assertStatus(401);
});

test('returns 404 when payment does not exist', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Act
    $response = $this->actingAs($user)
        ->getJson("/api/v1/payments/non-existent-id");
    
    // Assert
    $response->assertStatus(404);
});
