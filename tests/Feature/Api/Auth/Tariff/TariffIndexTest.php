<?php

namespace Tests\Feature\Api\Auth\Tariff;

use App\Models\Tariff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

uses(RefreshDatabase::class);

test('authenticated user can view tariffs', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Create tariffs
    $tariffs = Tariff::factory()->count(3)->create([
        'is_active' => true
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/tariffs');
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'currency',
                    'duration_days',
                    'features',
                    'is_active',
                    'slug',
                    'meta_title',
                    'meta_description',
                    'meta_image',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links',
            'meta'
        ]);
    
    // Check that we have 3 tariffs in the response
    $this->assertCount(3, $response->json('data'));
});

test('authenticated user can filter tariffs by active status', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Create active tariffs
    Tariff::factory()->count(2)->create([
        'is_active' => true
    ]);
    
    // Create inactive tariffs
    Tariff::factory()->count(1)->create([
        'is_active' => false
    ]);
    
    // Act - filter by active status
    $response = $this->actingAs($user)
        ->getJson('/api/v1/tariffs?is_active=1');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we only have active tariffs in the response
    $tariffs = $response->json('data');
    foreach ($tariffs as $tariff) {
        $this->assertTrue($tariff['is_active']);
    }
    
    // Check that we have 2 active tariffs
    $this->assertCount(2, $tariffs);
});

test('authenticated user can filter tariffs by currency', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Create tariffs with different currencies
    Tariff::factory()->count(2)->create([
        'currency' => 'UAH'
    ]);
    
    Tariff::factory()->count(1)->create([
        'currency' => 'USD'
    ]);
    
    // Act - filter by currency
    $response = $this->actingAs($user)
        ->getJson('/api/v1/tariffs?currency=UAH');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we only have UAH tariffs in the response
    $tariffs = $response->json('data');
    foreach ($tariffs as $tariff) {
        $this->assertEquals('UAH', $tariff['currency']);
    }
    
    // Check that we have 2 UAH tariffs
    $this->assertCount(2, $tariffs);
});

test('authenticated user can filter tariffs by price range', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Create tariffs with different prices
    Tariff::factory()->create([
        'price' => 100.00
    ]);
    
    Tariff::factory()->create([
        'price' => 200.00
    ]);
    
    Tariff::factory()->create([
        'price' => 300.00
    ]);
    
    // Act - filter by price range
    $response = $this->actingAs($user)
        ->getJson('/api/v1/tariffs?min_price=150&max_price=250');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that we only have tariffs within the price range in the response
    $tariffs = $response->json('data');
    foreach ($tariffs as $tariff) {
        $price = (float) $tariff['price'];
        $this->assertGreaterThanOrEqual(150, $price);
        $this->assertLessThanOrEqual(250, $price);
    }
    
    // Check that we have 1 tariff in the price range
    $this->assertCount(1, $tariffs);
});

test('authenticated user can sort tariffs by price', function () {
    // Arrange
    $user = User::factory()->create();
    
    // Create tariffs with different prices
    Tariff::factory()->create([
        'price' => 100.00
    ]);
    
    Tariff::factory()->create([
        'price' => 300.00
    ]);
    
    Tariff::factory()->create([
        'price' => 200.00
    ]);
    
    // Act - sort by price ascending
    $response = $this->actingAs($user)
        ->getJson('/api/v1/tariffs?sort=price&direction=asc');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that tariffs are sorted by price in ascending order
    $tariffs = $response->json('data');
    $prices = array_column($tariffs, 'price');
    $sortedPrices = $prices;
    sort($sortedPrices);
    
    $this->assertEquals($sortedPrices, $prices);
    
    // Act - sort by price descending
    $response = $this->actingAs($user)
        ->getJson('/api/v1/tariffs?sort=price&direction=desc');
    
    // Assert
    $response->assertStatus(200);
    
    // Check that tariffs are sorted by price in descending order
    $tariffs = $response->json('data');
    $prices = array_column($tariffs, 'price');
    $sortedPrices = $prices;
    rsort($sortedPrices);
    
    $this->assertEquals($sortedPrices, $prices);
});

test('unauthenticated user cannot view tariffs', function () {
    // Act
    $response = $this->getJson('/api/v1/tariffs');
    
    // Assert
    $response->assertStatus(401);
});
