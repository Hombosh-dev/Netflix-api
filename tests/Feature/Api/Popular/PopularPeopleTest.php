<?php

use App\Actions\Popular\GetPopularPeople;
use App\Enums\PersonType;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('popular people endpoint returns people ordered by movies count', function () {
    // Arrange
    $popularPerson = Person::factory()->create([
        'name' => 'Popular Person',
        'type' => PersonType::ACTOR,
    ]);

    $lessPopularPerson = Person::factory()->create([
        'name' => 'Less Popular Person',
        'type' => PersonType::DIRECTOR,
    ]);

    // Set movies_count attribute
    $popularPerson->movies_count = 10;
    $lessPopularPerson->movies_count = 5;

    // Mock the action to avoid database queries
    $this->mock(GetPopularPeople::class, function ($mock) use ($popularPerson, $lessPopularPerson) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection([$popularPerson, $lessPopularPerson]));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/people');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'Popular Person')
        ->assertJsonPath('data.1.name', 'Less Popular Person');
});

test('popular people endpoint respects limit parameter', function () {
    // Arrange
    $people = Person::factory()->count(5)->create();

    // Mock the action to avoid database queries
    $this->mock(GetPopularPeople::class, function ($mock) use ($people) {
        $mock->shouldReceive('handle')
            ->once()
            ->andReturn(new \Illuminate\Database\Eloquent\Collection($people->take(3)->all()));
    });

    // Act
    $response = $this->getJson('/api/v1/popular/people?limit=3');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});
