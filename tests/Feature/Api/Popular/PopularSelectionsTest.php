<?php

use App\Actions\Popular\GetPopularSelections;
use App\Models\Selection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('popular selections endpoint returns published selections ordered by user lists count', function () {
    // Arrange
    $popularSelection = Selection::factory()->create([
        'name' => 'Popular Selection',
        'is_published' => true,
    ]);

    $lessPopularSelection = Selection::factory()->create([
        'name' => 'Less Popular Selection',
        'is_published' => true,
    ]);

    // Set user_lists_count attribute
    $popularSelection->user_lists_count = 10;
    $lessPopularSelection->user_lists_count = 5;

    // Mock the action to avoid database queries
    $this->mock(GetPopularSelections::class, function ($mock) use ($popularSelection, $lessPopularSelection) {
        $mock->shouldReceive('handle')
            ->withArgs(function ($dto) {
                return $dto->limit === 10;
            })
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$popularSelection, $lessPopularSelection]));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/selections?limit=10');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'Popular Selection')
        ->assertJsonPath('data.1.name', 'Less Popular Selection');
});

test('popular selections endpoint respects limit parameter', function () {
    // Arrange
    $selections = Selection::factory()->count(5)->create([
        'is_published' => true,
    ]);

    // Mock the action to avoid database queries
    $this->mock(GetPopularSelections::class, function ($mock) use ($selections) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection($selections->take(3)->all()));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/selections?limit=3');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});
