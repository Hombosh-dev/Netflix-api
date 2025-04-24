<?php

use App\Actions\Search\PerformAutocomplete;
use App\DTOs\Search\AutocompleteDTO;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('it returns empty results for empty query', function () {
    // Arrange
    $action = new PerformAutocomplete();
    $dto = new AutocompleteDTO(
        query: ''
    );

    // Act
    $results = $action->handle($dto);

    // Assert
    expect($results)->toBeArray()
        ->toBeEmpty();
});

test('it returns empty results for short query', function () {
    // Arrange
    $action = new PerformAutocomplete();
    $dto = new AutocompleteDTO(
        query: 'a' // Less than 2 characters
    );

    // Act
    $results = $action->handle($dto);

    // Assert
    expect($results)->toBeArray()
        ->toBeEmpty();
});

test('autocomplete dto has correct structure', function () {
    // Arrange
    $dto = new AutocompleteDTO(
        query: 'test query'
    );

    // Assert
    expect($dto->query)->toBe('test query');
});

test('autocomplete dto can be created from request', function () {
    // Arrange
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'q' => 'test query'
    ]);

    // Act
    $dto = AutocompleteDTO::fromRequest($request);

    // Assert
    expect($dto->query)->toBe('test query');
});
