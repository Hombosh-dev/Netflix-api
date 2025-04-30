<?php

use App\Models\Movie;
use App\Models\Studio;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('persons endpoint returns persons associated with a movie', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Test Movie',
        'is_published' => true
    ]);

    // Create persons and associate them with the movie
    $person1 = Person::factory()->create([
        'name' => 'Actor 1'
    ]);

    $person2 = Person::factory()->create([
        'name' => 'Director 1'
    ]);

    $movie->persons()->attach($person1->id, ['character_name' => 'Character 1']);
    $movie->persons()->attach($person2->id, ['character_name' => 'Director']);

    // Act
    $response = $this->getJson("/api/v1/movies/{$movie->slug}/persons");

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'image'
                ]
            ]
        ]);

    $personNames = collect($response->json('data'))->pluck('name')->all();
    expect($personNames)->toContain('Actor 1')
        ->toContain('Director 1');
});
