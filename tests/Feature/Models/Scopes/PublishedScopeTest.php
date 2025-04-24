<?php

use App\Models\Movie;
use App\Models\Scopes\PublishedScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Тест для перевірки правильності застосування скоупу PublishedScope
test('published scope applies correctly', function () {
    // Create a mock builder
    $builder = Mockery::mock(Builder::class);
    $model = Mockery::mock(Model::class);

    // Set up expectations
    $builder->shouldReceive('where')
        ->once()
        ->with('is_published', true)
        ->andReturnSelf();

    // Apply the scope
    $scope = new PublishedScope();
    $scope->apply($builder, $model);
});

// Тест для перевірки, що скоуп PublishedScope застосовується до моделі Movie
test('published scope is applied to movie model', function () {
    // Get the base query for Movie model
    $query = Movie::query();
    $sql = $query->toSql();

    // Check that the published scope is applied
    expect($sql)->toContain('"is_published" = ?')
        ->and($query->getBindings())->toContain(true);
});

// Тест для перевірки, що скоуп PublishedScope фільтрує неопубліковані фільми
test('published scope filters out unpublished movies', function () {
    // Arrange - створюємо фільми
    $unpublishedMovie = Movie::factory()->create(['is_published' => false]);
    $publishedMovie = Movie::factory()->create(['is_published' => true]);
    
    // Act - отримуємо всі фільми (скоуп застосовується автоматично)
    $movies = Movie::all();
    
    // Assert - перевіряємо, що неопублікований фільм не включений у результат
    expect($movies)->toHaveCount(1)
        ->and($movies->first()->id)->toBe($publishedMovie->id)
        ->and($movies->contains($unpublishedMovie))->toBeFalse();
});

// Тест для перевірки, що можна отримати неопубліковані фільми, якщо явно вказати
test('can get unpublished movies when explicitly requested', function () {
    // Arrange - створюємо фільми
    $unpublishedMovie = Movie::factory()->create(['is_published' => false]);
    $publishedMovie = Movie::factory()->create(['is_published' => true]);
    
    // Act - отримуємо всі фільми, включаючи неопубліковані
    $allMovies = Movie::withoutGlobalScope(PublishedScope::class)->get();
    
    // Assert - перевіряємо, що обидва фільми включені у результат
    expect($allMovies)->toHaveCount(2)
        ->and($allMovies->contains($unpublishedMovie))->toBeTrue()
        ->and($allMovies->contains($publishedMovie))->toBeTrue();
});
