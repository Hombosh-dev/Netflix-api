<?php

use App\Actions\Search\PerformAutocomplete;
use App\Actions\Search\PerformSearch;
use App\DTOs\Search\AutocompleteDTO;
use App\DTOs\Search\SearchDTO;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Mockery\MockInterface;

test('search method calls action with correct DTO', function () {
    // Arrange
    $request = Request::create('/search', 'GET', [
        'q' => 'test query',
        'types' => ['movies', 'people'],
    ]);

    $mockAction = $this->mock(PerformSearch::class, function (MockInterface $mock) {
        $mock->shouldReceive('handle')
            ->once()
            ->withArgs(function (SearchDTO $dto) {
                return $dto->query === 'test query' && 
                       $dto->types === ['movies', 'people'];
            })
            ->andReturn(['movies' => [], 'people' => []]);
    });

    $controller = new SearchController();

    // Act
    $response = $controller->search($request, $mockAction);

    // Assert
    expect($response->getStatusCode())->toBe(200)
        ->and(json_decode($response->getContent(), true))->toBe(['movies' => [], 'people' => []]);
});

test('autocomplete method calls action with correct DTO', function () {
    // Arrange
    $request = Request::create('/search/autocomplete', 'GET', [
        'q' => 'test query',
    ]);

    $mockAction = $this->mock(PerformAutocomplete::class, function (MockInterface $mock) {
        $mock->shouldReceive('handle')
            ->once()
            ->withArgs(function (AutocompleteDTO $dto) {
                return $dto->query === 'test query';
            })
            ->andReturn([
                ['id' => '1', 'text' => 'Test Movie', 'type' => 'movie'],
                ['id' => '2', 'text' => 'Test Person', 'type' => 'person'],
            ]);
    });

    $controller = new SearchController();

    // Act
    $response = $controller->autocomplete($request, $mockAction);

    // Assert
    expect($response->getStatusCode())->toBe(200)
        ->and(json_decode($response->getContent(), true))->toBe([
            ['id' => '1', 'text' => 'Test Movie', 'type' => 'movie'],
            ['id' => '2', 'text' => 'Test Person', 'type' => 'person'],
        ]);
});
