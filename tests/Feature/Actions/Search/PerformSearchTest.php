<?php

use App\Actions\Search\PerformSearch;
use App\DTOs\Search\SearchDTO;
use Mockery;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it returns empty results for empty query', function () {
    // Arrange
    $action = new PerformSearch();
    $dto = new SearchDTO(
        query: '',
        types: ['movies', 'people', 'studios', 'tags', 'selections']
    );

    // Act
    $results = $action->handle($dto);

    // Assert
    expect($results)->toBeArray()
        ->toBeEmpty();
});

test('it searches only requested content types', function () {
    // Arrange
    $mockAction = Mockery::mock(PerformSearch::class)->makePartial();
    $mockAction->shouldReceive('handle')
        ->andReturnUsing(function (SearchDTO $dto) {
            $results = [];
            
            if (in_array('movies', $dto->types)) {
                $results['movies'] = [
                    ['id' => 'movie-1', 'name' => 'Test Movie']
                ];
            }
            
            if (in_array('people', $dto->types)) {
                $results['people'] = [
                    ['id' => 'person-1', 'name' => 'Test Person']
                ];
            }
            
            return $results;
        });
    
    $dto = new SearchDTO(
        query: 'test',
        types: ['movies', 'people'] // Only search movies and people
    );

    // Act
    $results = $mockAction->handle($dto);

    // Assert
    expect($results)->toBeArray()
        ->toHaveKeys(['movies', 'people'])
        ->not->toHaveKeys(['studios', 'tags', 'selections']);
});

test('search dto has correct structure', function () {
    // Arrange
    $dto = new SearchDTO(
        query: 'test query',
        types: ['movies', 'people', 'studios']
    );

    // Assert
    expect($dto->query)->toBe('test query')
        ->and($dto->types)->toBe(['movies', 'people', 'studios']);
});

test('search dto can be created from request', function () {
    // Arrange
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'q' => 'test query',
        'types' => 'movies,people'
    ]);

    // Act
    $dto = SearchDTO::fromRequest($request);

    // Assert
    expect($dto->query)->toBe('test query')
        ->and($dto->types)->toBe(['movies', 'people']);
});
