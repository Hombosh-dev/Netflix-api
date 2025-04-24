<?php

use App\Actions\Popular\GetPopularTags;
use App\DTOs\Popular\PopularTagsDTO;
use App\Models\Movie;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns popular tags ordered by movies count', function () {
    // Arrange
    $popularTag = Tag::factory()->create();
    $lessPopularTag = Tag::factory()->create();
    
    // Create movies and attach them to tags
    $movies1 = Movie::factory()->count(5)->create();
    $movies2 = Movie::factory()->count(2)->create();
    
    $popularTag->movies()->attach($movies1->pluck('id')->toArray());
    $lessPopularTag->movies()->attach($movies2->pluck('id')->toArray());
    
    $action = new GetPopularTags();
    $dto = new PopularTagsDTO(limit: 10);

    // Act
    $tags = $action->handle($dto);

    // Assert
    expect($tags)->toHaveCount(2)
        ->and($tags->first()->id)->toBe($popularTag->id)
        ->and($tags->first()->movies_count)->toBe(5);
});

test('it respects the limit parameter', function () {
    // Arrange
    Tag::factory()->count(5)->create();
    
    $action = new GetPopularTags();
    $dto = new PopularTagsDTO(limit: 3);

    // Act
    $tags = $action->handle($dto);

    // Assert
    expect($tags)->toHaveCount(3);
});
