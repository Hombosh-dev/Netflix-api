<?php

use App\Actions\Popular\GetPopularPeople;
use App\DTOs\Popular\PopularPeopleDTO;
use App\Models\Movie;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns popular people ordered by movies count', function () {
    // Arrange
    $personWithManyMovies = Person::factory()->create();
    $personWithFewMovies = Person::factory()->create();
    
    // Create movies and attach them to people
    $movies1 = Movie::factory()->count(5)->create();
    $movies2 = Movie::factory()->count(2)->create();
    
    $personWithManyMovies->movies()->attach($movies1->pluck('id')->toArray());
    $personWithFewMovies->movies()->attach($movies2->pluck('id')->toArray());
    
    $action = new GetPopularPeople();
    $dto = new PopularPeopleDTO(limit: 10);

    // Act
    $people = $action->handle($dto);

    // Assert
    expect($people)->toHaveCount(2)
        ->and($people->first()->id)->toBe($personWithManyMovies->id)
        ->and($people->first()->movies_count)->toBe(5);
});

test('it respects the limit parameter', function () {
    // Arrange
    Person::factory()->count(5)->create();
    
    $action = new GetPopularPeople();
    $dto = new PopularPeopleDTO(limit: 3);

    // Act
    $people = $action->handle($dto);

    // Assert
    expect($people)->toHaveCount(3);
});
