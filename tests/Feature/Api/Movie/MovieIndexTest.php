<?php

use App\Enums\Kind;
use App\Enums\Status;
use App\Models\Movie;
use App\Models\Studio;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index endpoint returns paginated movies', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movies = Movie::factory()->count(3)->create([
        'studio_id' => $studio->id,
        'is_published' => true
    ]);

    // Act
    $response = $this->getJson('/api/v1/movies');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'kind',
                    'status',
                    'poster',
                    'imdb_score',
                    'year',
                ]
            ],
            'links',
            'meta'
        ]);
});

test('index endpoint respects pagination parameters', function () {
    // Arrange
    $studio = Studio::factory()->create();
    $movies = Movie::factory()->count(10)->create([
        'studio_id' => $studio->id,
        'is_published' => true
    ]);

    // Act
    $response = $this->getJson('/api/v1/movies?page=2&per_page=3');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.per_page', 3);
});

test('index endpoint can filter by search query', function () {
    // Arrange
    $studio = Studio::factory()->create();
    
    // Create movies with specific names for testing search
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Star Wars: A New Hope',
        'is_published' => true
    ]);
    
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Star Trek',
        'is_published' => true
    ]);
    
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'The Godfather',
        'is_published' => true
    ]);

    // Act
    $response = $this->getJson('/api/v1/movies?q=star');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
    
    // Get the names from the response
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toContain('Star Wars: A New Hope')
        ->toContain('Star Trek');
});

test('index endpoint can filter by kind', function () {
    // Arrange
    $studio = Studio::factory()->create();
    
    // Create movies with different kinds
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Movie 1',
        'kind' => Kind::MOVIE,
        'is_published' => true
    ]);
    
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Series 1',
        'kind' => Kind::TV_SERIES,
        'is_published' => true
    ]);
    
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Animated Movie',
        'kind' => Kind::ANIMATED_MOVIE,
        'is_published' => true
    ]);

    // Act
    $response = $this->getJson('/api/v1/movies?kinds=movie,animated_movie');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
    
    $kinds = collect($response->json('data'))->pluck('kind')->all();
    expect($kinds)->toContain(Kind::MOVIE->value)
        ->toContain(Kind::ANIMATED_MOVIE->value)
        ->not->toContain(Kind::TV_SERIES->value);
});

test('index endpoint can filter by status', function () {
    // Arrange
    $studio = Studio::factory()->create();
    
    // Create movies with different statuses
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Ongoing Movie',
        'status' => Status::ONGOING,
        'is_published' => true
    ]);
    
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Released Movie',
        'status' => Status::RELEASED,
        'is_published' => true
    ]);
    
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Announced Movie',
        'status' => Status::ANONS,
        'is_published' => true
    ]);

    // Act
    $response = $this->getJson('/api/v1/movies?statuses=ongoing,anons');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
    
    $statuses = collect($response->json('data'))->pluck('status')->all();
    expect($statuses)->toContain(Status::ONGOING->value)
        ->toContain(Status::ANONS->value)
        ->not->toContain(Status::RELEASED->value);
});

test('index endpoint can filter by imdb score range', function () {
    // Arrange
    $studio = Studio::factory()->create();
    
    // Create movies with different IMDb scores
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'High Rated Movie',
        'imdb_score' => 9.0,
        'is_published' => true
    ]);
    
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Medium Rated Movie',
        'imdb_score' => 7.5,
        'is_published' => true
    ]);
    
    Movie::factory()->create([
        'studio_id' => $studio->id,
        'name' => 'Low Rated Movie',
        'imdb_score' => 5.0,
        'is_published' => true
    ]);

    // Act
    $response = $this->getJson('/api/v1/movies?min_score=7.0&max_score=8.0');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Medium Rated Movie');
});
