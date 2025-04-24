<?php

use App\Actions\Popular\GetPopularSelections;
use App\DTOs\Popular\PopularSelectionsDTO;
use App\Models\Selection;
use App\Models\UserList;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it returns published selections ordered by user lists count', function () {
    // Arrange
    $popularSelection = Selection::factory()->create([
        'is_published' => true,
    ]);
    
    $lessPopularSelection = Selection::factory()->create([
        'is_published' => true,
    ]);
    
    $unpublishedSelection = Selection::factory()->create([
        'is_published' => false,
    ]);
    
    // Create user lists and attach them to selections
    UserList::factory()->count(5)->create([
        'selection_id' => $popularSelection->id,
    ]);
    
    UserList::factory()->count(2)->create([
        'selection_id' => $lessPopularSelection->id,
    ]);
    
    UserList::factory()->count(10)->create([
        'selection_id' => $unpublishedSelection->id,
    ]);
    
    $action = new GetPopularSelections();
    $dto = new PopularSelectionsDTO(limit: 10);

    // Act
    $selections = $action->handle($dto);

    // Assert
    expect($selections)->toHaveCount(2)
        ->and($selections->first()->id)->toBe($popularSelection->id)
        ->and($selections->first()->user_lists_count)->toBe(5)
        ->and($selections->pluck('id')->toArray())->not->toContain($unpublishedSelection->id);
});

test('it respects the limit parameter', function () {
    // Arrange
    Selection::factory()->count(5)->create([
        'is_published' => true,
    ]);
    
    $action = new GetPopularSelections();
    $dto = new PopularSelectionsDTO(limit: 3);

    // Act
    $selections = $action->handle($dto);

    // Assert
    expect($selections)->toHaveCount(3);
});
