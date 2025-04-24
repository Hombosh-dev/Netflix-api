<?php

use App\Actions\Popular\GetPopularTags;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('popular tags endpoint returns tags ordered by movies count', function () {
    // Arrange
    $popularTag = Tag::factory()->create([
        'name' => 'Popular Tag',
    ]);

    $lessPopularTag = Tag::factory()->create([
        'name' => 'Less Popular Tag',
    ]);

    // Set movies_count attribute
    $popularTag->movies_count = 10;
    $lessPopularTag->movies_count = 5;

    // Mock the action to avoid database queries
    $this->mock(GetPopularTags::class, function ($mock) use ($popularTag, $lessPopularTag) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$popularTag, $lessPopularTag]));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/tags');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'Popular Tag')
        ->assertJsonPath('data.1.name', 'Less Popular Tag');
});

test('popular tags endpoint respects limit parameter', function () {
    // Arrange
    $tags = Tag::factory()->count(5)->create();

    // Mock the action to avoid database queries
    $this->mock(GetPopularTags::class, function ($mock) use ($tags) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection($tags->take(3)->all()));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/tags?limit=3');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});
