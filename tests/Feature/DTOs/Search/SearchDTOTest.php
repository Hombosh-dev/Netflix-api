<?php

use App\DTOs\Search\SearchDTO;
use Illuminate\Http\Request;

test('it can be created from request', function () {
    // Arrange
    $request = Request::create('/search', 'GET', [
        'q' => 'test query',
        'types' => ['movies', 'people'],
    ]);

    // Act
    $dto = SearchDTO::fromRequest($request);

    // Assert
    expect($dto)->toBeInstanceOf(SearchDTO::class)
        ->and($dto->query)->toBe('test query')
        ->and($dto->types)->toBe(['movies', 'people']);
});

test('it handles string types parameter', function () {
    // Arrange
    $request = Request::create('/search', 'GET', [
        'q' => 'test query',
        'types' => 'movies,people',
    ]);

    // Act
    $dto = SearchDTO::fromRequest($request);

    // Assert
    expect($dto)->toBeInstanceOf(SearchDTO::class)
        ->and($dto->query)->toBe('test query')
        ->and($dto->types)->toBe(['movies', 'people']);
});

test('it uses default types when not provided', function () {
    // Arrange
    $request = Request::create('/search', 'GET', [
        'q' => 'test query',
    ]);

    // Act
    $dto = SearchDTO::fromRequest($request);

    // Assert
    expect($dto)->toBeInstanceOf(SearchDTO::class)
        ->and($dto->query)->toBe('test query')
        ->and($dto->types)->toBe(['movies', 'people', 'studios', 'tags', 'selections']);
});

test('it can be converted to array', function () {
    // Arrange
    $dto = new SearchDTO(
        query: 'test query',
        types: ['movies', 'people']
    );

    // Act
    $array = $dto->toArray();

    // Assert
    expect($array)->toBe([
        'query' => 'test query',
        'types' => ['movies', 'people'],
    ]);
});
