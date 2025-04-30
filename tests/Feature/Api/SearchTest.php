<?php

use App\Actions\Search\PerformAutocomplete;
use App\Actions\Search\PerformSearch;
use App\DTOs\Search\AutocompleteDTO;
use App\DTOs\Search\SearchDTO;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Studio;
use App\Models\Tag;
use App\Enums\Kind;
use App\Enums\PersonType;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('search endpoint returns results for all content types', function () {
    // Мокуємо Action класи замість моделей
    $this->mock(PerformSearch::class, function ($mock) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn([
                'movies' => [
                    ['id' => 'movie-1', 'name' => 'Test Movie', 'slug' => 'test-movie', 'image' => 'movie.jpg', 'kind' => Kind::MOVIE->value, 'year' => 2023]
                ],
                'people' => [
                    ['id' => 'person-1', 'name' => 'Test Person', 'slug' => 'test-person', 'image' => 'person.jpg', 'type' => PersonType::ACTOR->value]
                ],
                'studios' => [
                    ['id' => 'studio-1', 'name' => 'Test Studio', 'slug' => 'test-studio', 'image' => 'studio.jpg']
                ],
                'tags' => [
                    ['id' => 'tag-1', 'name' => 'Test Tag', 'slug' => 'test-tag', 'image' => 'tag.jpg']
                ],
                'selections' => [
                    ['id' => 'selection-1', 'name' => 'Test Selection', 'slug' => 'test-selection', 'image' => 'selection.jpg']
                ]
            ]);
    });

    // Виконуємо запит до API
    $response = $this->getJson('/api/v1/search?q=test');

    // Перевіряємо відповідь
    $response->assertStatus(200)
        ->assertJsonStructure([
            'movies',
            'people',
            'studios',
            'tags',
            'selections'
        ]);

    // Перевіряємо, що результати містять наші тестові дані
    $response->assertJson([
        'movies' => [
            [
                'name' => 'Test Movie',
                'kind' => Kind::MOVIE->value,
            ]
        ],
        'people' => [
            [
                'name' => 'Test Person',
                'type' => PersonType::ACTOR->value,
            ]
        ],
        'studios' => [
            [
                'name' => 'Test Studio',
            ]
        ],
        'tags' => [
            [
                'name' => 'Test Tag',
            ]
        ],
        'selections' => [
            [
                'name' => 'Test Selection',
            ]
        ]
    ]);
});

test('search endpoint returns results for specific content types', function () {
    // Мокуємо Action класи замість моделей
    $this->mock(PerformSearch::class, function ($mock) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn([
                'movies' => [
                    ['id' => 'movie-1', 'name' => 'Test Movie', 'slug' => 'test-movie', 'image' => 'movie.jpg', 'kind' => Kind::MOVIE->value, 'year' => 2023]
                ],
                'people' => [
                    ['id' => 'person-1', 'name' => 'Test Person', 'slug' => 'test-person', 'image' => 'person.jpg', 'type' => PersonType::ACTOR->value]
                ]
            ]);
    });

    // Виконуємо запит до API з фільтрацією типів контенту
    $response = $this->getJson('/api/v1/search?q=test&types=movies,people');

    // Перевіряємо відповідь
    $response->assertStatus(200)
        ->assertJsonStructure([
            'movies',
            'people'
        ])
        ->assertJsonMissing([
            'studios',
            'tags',
            'selections'
        ]);

    // Перевіряємо, що результати містять наші тестові дані
    $response->assertJson([
        'movies' => [
            [
                'name' => 'Test Movie',
                'kind' => Kind::MOVIE->value,
            ]
        ],
        'people' => [
            [
                'name' => 'Test Person',
                'type' => PersonType::ACTOR->value,
            ]
        ]
    ]);
});

test('search endpoint returns empty results for empty query', function () {
    // Мокуємо Action класи замість моделей
    $this->mock(PerformSearch::class, function ($mock) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn([]);
    });

    // Виконуємо запит до API з порожнім запитом
    $response = $this->getJson('/api/v1/search?q=');

    // Перевіряємо відповідь
    $response->assertStatus(200)
        ->assertJson([]);
});

test('autocomplete endpoint returns results', function () {
    // Мокуємо Action класи замість моделей
    $this->mock(PerformAutocomplete::class, function ($mock) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn([
                [
                    'id' => 'movie-1',
                    'text' => 'Test Movie Autocomplete',
                    'type' => 'movie',
                    'image' => 'movie.jpg',
                    'url' => '/movies/test-movie-autocomplete'
                ],
                [
                    'id' => 'person-1',
                    'text' => 'Test Person Autocomplete',
                    'type' => 'person',
                    'image' => 'person.jpg',
                    'url' => '/people/test-person-autocomplete'
                ],
                [
                    'id' => 'tag-1',
                    'text' => 'Test Tag Autocomplete',
                    'type' => 'tag',
                    'image' => 'tag.jpg',
                    'url' => '/tags/test-tag-autocomplete'
                ]
            ]);
    });

    // Виконуємо запит до API
    $response = $this->getJson('/api/v1/search/autocomplete?q=test');

    // Перевіряємо відповідь
    $response->assertStatus(200)
        ->assertJsonCount(3)
        ->assertJsonStructure([
            '*' => [
                'id',
                'text',
                'type',
                'image',
                'url'
            ]
        ]);

    // Перевіряємо, що результати містять наші тестові дані
    $response->assertJson([
        [
            'text' => 'Test Movie Autocomplete',
            'type' => 'movie',
            'url' => '/movies/test-movie-autocomplete'
        ],
        [
            'text' => 'Test Person Autocomplete',
            'type' => 'person',
            'url' => '/people/test-person-autocomplete'
        ],
        [
            'text' => 'Test Tag Autocomplete',
            'type' => 'tag',
            'url' => '/tags/test-tag-autocomplete'
        ]
    ]);
});

test('autocomplete endpoint returns empty results for short query', function () {
    // Мокуємо Action класи замість моделей
    $this->mock(PerformAutocomplete::class, function ($mock) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn([]);
    });

    // Виконуємо запит до API з коротким запитом
    $response = $this->getJson('/api/v1/search/autocomplete?q=a');

    // Перевіряємо відповідь
    $response->assertStatus(200)
        ->assertJson([]);
});
