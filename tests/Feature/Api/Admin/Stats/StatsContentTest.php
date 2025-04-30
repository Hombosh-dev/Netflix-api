<?php

namespace Tests\Feature\Api\Stats;

use App\Models\User;
use App\Models\Movie;
use App\Models\Episode;
use App\Models\Person;
use App\Models\Studio;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view content stats', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some content for stats
    $movies = Movie::factory()->count(3)->create();

    // Create episodes with unique movie_id and number combinations
    foreach ($movies as $index => $movie) {
        Episode::factory()->create([
            'movie_id' => $movie->id,
            'number' => $index + 1
        ]);
    }

    Person::factory()->count(2)->create();
    Studio::factory()->count(1)->create();
    Tag::factory()->count(4)->create();

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/stats/content');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'movies' => [
                    'total',
                    'new_this_period',
                    'by_kind'
                ],
                'episodes' => [
                    'total',
                    'new_this_period'
                ],
                'people' => [
                    'total',
                    'new_this_period'
                ],
                'studios' => [
                    'total',
                    'new_this_period'
                ],
                'tags' => [
                    'total',
                    'new_this_period'
                ],
                'selections' => [
                    'total',
                    'new_this_period'
                ],
                'comments' => [
                    'total',
                    'new_this_period'
                ],
                'ratings' => [
                    'total',
                    'new_this_period'
                ],
                'user_lists' => [
                    'total',
                    'new_this_period'
                ]
            ]
        ]);
});

test('admin can view content stats with custom days parameter', function () {
    // Arrange
    $admin = User::factory()->create(['role' => 'admin']);

    // Create some content for stats
    $movies = Movie::factory()->count(3)->create();

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/stats/content?days=30');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'movies' => [
                    'total',
                    'new_this_period',
                    'by_kind'
                ]
            ]
        ]);
});

test('non-admin cannot view content stats', function () {
    // Arrange
    $user = User::factory()->create(['role' => 'user']);

    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/admin/stats/content');

    // Assert
    $response->assertStatus(403);
});

test('unauthenticated user cannot view content stats', function () {
    // Act
    $response = $this->getJson('/api/v1/admin/stats/content');

    // Assert
    $response->assertStatus(401);
});
