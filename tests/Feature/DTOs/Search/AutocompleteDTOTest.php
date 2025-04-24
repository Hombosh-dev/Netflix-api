<?php

use App\DTOs\Search\AutocompleteDTO;
use Illuminate\Http\Request;

test('it can be created from request', function () {
    // Arrange
    $request = Request::create('/search/autocomplete', 'GET', [
        'q' => 'test query',
    ]);

    // Act
    $dto = AutocompleteDTO::fromRequest($request);

    // Assert
    expect($dto)->toBeInstanceOf(AutocompleteDTO::class)
        ->and($dto->query)->toBe('test query');
});

test('it handles empty query', function () {
    // Arrange
    $request = Request::create('/search/autocomplete', 'GET', []);

    // Act
    $dto = AutocompleteDTO::fromRequest($request);

    // Assert
    expect($dto)->toBeInstanceOf(AutocompleteDTO::class)
        ->and($dto->query)->toBe('');
});

test('it can be converted to array', function () {
    // Arrange
    $dto = new AutocompleteDTO(
        query: 'test query'
    );

    // Act
    $array = $dto->toArray();

    // Assert
    expect($array)->toBe([
        'query' => 'test query',
    ]);
});
