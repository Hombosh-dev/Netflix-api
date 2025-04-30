<?php

namespace Tests\Feature\Api\Auth\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view their payments', function () {
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
        ->getJson('/api/v1/payments');
    
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
});

test('authenticated user can filter payments by status', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create payments with different statuses
    Payment::factory()->count(2)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'status' => PaymentStatus::SUCCESS
    ]);
    
    Payment::factory()->count(1)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'status' => PaymentStatus::PENDING
    ]);
    
    // Act - filter by success status
    $response = $this->actingAs($user)
        ->getJson('/api/v1/payments?status=success');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we only have successful payments in the response
    $payments = $response->json('data');
    foreach ($payments as $payment) {
        $this->assertEquals('success', $payment['status']);
    }
    
    // Check that we have 2 successful payments
    $this->assertCount(2, $payments);
});

test('authenticated user can filter payments by date range', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create payments with different dates
    Payment::factory()->count(2)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'created_at' => now()->subDays(10)
    ]);
    
    Payment::factory()->count(3)->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'created_at' => now()->subDays(5)
    ]);
    
    // Act - filter by date range
    $response = $this->actingAs($user)
        ->getJson('/api/v1/payments?date_from=' . now()->subDays(7)->toDateString() . '&date_to=' . now()->toDateString());
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we have 3 payments in the response (only the recent ones)
    $this->assertCount(3, $response->json('data'));
});

test('authenticated user can sort payments', function () {
    // Arrange
    $user = User::factory()->create();
    $tariff = Tariff::factory()->create();
    
    // Create payments with different amounts
    Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 100.00
    ]);
    
    Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 200.00
    ]);
    
    Payment::factory()->create([
        'user_id' => $user->id,
        'tariff_id' => $tariff->id,
        'amount' => 50.00
    ]);
    
    // Act - sort by amount ascending
    $response = $this->actingAs($user)
        ->getJson('/api/v1/payments?sort=amount&direction=asc');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that payments are sorted by amount in ascending order
    $payments = $response->json('data');
    $amounts = array_column($payments, 'amount');
    $sortedAmounts = $amounts;
    sort($sortedAmounts);
    
    $this->assertEquals($sortedAmounts, $amounts);
    
    // Act - sort by amount descending
    $response = $this->actingAs($user)
        ->getJson('/api/v1/payments?sort=amount&direction=desc');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that payments are sorted by amount in descending order
    $payments = $response->json('data');
    $amounts = array_column($payments, 'amount');
    $sortedAmounts = $amounts;
    rsort($sortedAmounts);
    
    $this->assertEquals($sortedAmounts, $amounts);
});

test('unauthenticated user cannot view payments', function () {
    // Act
    $response = $this->getJson('/api/v1/payments');
    
    // Assert
    $response->assertStatus(401);
});
