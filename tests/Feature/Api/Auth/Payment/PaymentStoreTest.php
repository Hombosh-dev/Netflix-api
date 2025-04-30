<?php

namespace Tests\Feature\Api\Auth\Payment;

use App\Enums\PaymentStatus;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create a payment', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true
    ]);
    
    $paymentData = [
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => 'txn_' . uniqid(),
        'status' => PaymentStatus::SUCCESS->value,
        'liqpay_data' => [
            'order_id' => 'order_123',
            'payment_id' => 'payment_456'
        ]
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/payments', $paymentData);
    
    // Assert
    $response->assertStatus(201)
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
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.tariff_id', $tariff->id)
        ->assertJsonPath('data.amount', (string)$paymentData['amount'])
        ->assertJsonPath('data.currency', $paymentData['currency'])
        ->assertJsonPath('data.payment_method', $paymentData['payment_method'])
        ->assertJsonPath('data.status', $paymentData['status']);
    
    // Check that the payment was created in the database
    $this->assertDatabaseHas('payments', [
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => $paymentData['amount'],
        'currency' => $paymentData['currency'],
        'payment_method' => $paymentData['payment_method'],
        'transaction_id' => $paymentData['transaction_id']
    ]);
    
    // Check that a subscription was created for the successful payment
    $this->assertDatabaseHas('user_subscriptions', [
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'is_active' => true
    ]);
});

test('successful payment extends existing subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true
    ]);
    
    // Create an existing subscription
    $existingSubscription = UserSubscription::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'start_date' => now()->subDays(15),
        'end_date' => now()->addDays(15), // 15 days remaining
        'is_active' => true
    ]);
    
    $paymentData = [
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => 'txn_' . uniqid(),
        'status' => PaymentStatus::SUCCESS->value
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/payments', $paymentData);
    
    // Assert
    $response->assertStatus(201);
    
    // Check that the subscription was extended (15 days remaining + 30 days from tariff = 45 days)
    $updatedSubscription = UserSubscription::find($existingSubscription->id);
    $this->assertTrue($updatedSubscription->end_date->isAfter(now()->addDays(40)));
});

test('pending payment does not create subscription', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create([
        'price' => 199.99,
        'currency' => 'UAH',
        'duration_days' => 30,
        'is_active' => true
    ]);
    
    $paymentData = [
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card',
        'transaction_id' => 'txn_' . uniqid(),
        'status' => PaymentStatus::PENDING->value
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/payments', $paymentData);
    
    // Assert
    $response->assertStatus(201);
    
    // Check that no subscription was created for the pending payment
    $this->assertDatabaseMissing('user_subscriptions', [
        'user_id' => $user->id,
        'tariff_id' => $tariff->id
    ]);
});

test('validation fails when required fields are missing', function () {
    // Arrange
    $user = User::factory()->create();
    
    $paymentData = [
        // Missing required fields
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/payments', $paymentData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['tariff_id', 'amount', 'currency', 'payment_method']);
});

test('unauthenticated user cannot create a payment', function () {
    // Arrange
    $tariff = Tariff::factory()->create();
    
    $paymentData = [
        'tariff_id' => $tariff->id,
        'amount' => 199.99,
        'currency' => 'UAH',
        'payment_method' => 'card'
    ];
    
    // Act
    $response = $this->postJson('/api/v1/payments', $paymentData);
    
    // Assert
    $response->assertStatus(401);
});
