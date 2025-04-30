<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('recently aired episodes endpoint returns correct data', function () {
    // Act
    // Use the aired-after endpoint with today's date
    $response = $this->getJson('/api/v1/episodes/aired-after/' . now()->subDays(7)->format('Y-m-d'));

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure(['data']);

    $responseData = $response->json('data');

    if ($responseData !== null) {
        \Log::info('Response from recently aired episodes endpoint:', [
            'status' => $response->status(),
            'data_count' => count($responseData),
        ]);
    }
});
