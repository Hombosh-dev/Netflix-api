<?php

use App\Models\Scopes\BannedScope;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Тест для перевірки правильності застосування скоупу BannedScope
test('banned scope applies correctly', function () {
    // Create a mock builder
    $builder = Mockery::mock(Builder::class);
    $model = Mockery::mock(Model::class);

    // Set up expectations
    $builder->shouldReceive('where')
        ->once()
        ->with('is_banned', false)
        ->andReturnSelf();

    // Apply the scope
    $scope = new BannedScope();
    $scope->apply($builder, $model);
});

// Тест для перевірки, що скоуп BannedScope застосовується до моделі User
test('banned scope is applied to user model', function () {
    // Get the base query for User model
    $query = User::query();
    $sql = $query->toSql();

    // Check that the banned scope is applied
    expect($sql)->toContain('"is_banned" = ?')
        ->and($query->getBindings())->toContain(false);
});

// Тест для перевірки, що скоуп BannedScope фільтрує заблокованих користувачів
test('banned scope filters out banned users', function () {
    // Arrange - створюємо користувачів
    $bannedUser = User::factory()->create(['is_banned' => true]);
    $activeUser = User::factory()->create(['is_banned' => false]);
    
    // Act - отримуємо всіх користувачів (скоуп застосовується автоматично)
    $users = User::all();
    
    // Assert - перевіряємо, що заблокований користувач не включений у результат
    expect($users)->toHaveCount(1)
        ->and($users->first()->id)->toBe($activeUser->id)
        ->and($users->contains($bannedUser))->toBeFalse();
});

// Тест для перевірки, що можна отримати заблокованих користувачів, якщо явно вказати
test('can get banned users when explicitly requested', function () {
    // Arrange - створюємо користувачів
    $bannedUser = User::factory()->create(['is_banned' => true]);
    $activeUser = User::factory()->create(['is_banned' => false]);
    
    // Act - отримуємо всіх користувачів, включаючи заблокованих
    $allUsers = User::withoutGlobalScope(BannedScope::class)->get();
    
    // Assert - перевіряємо, що обидва користувачі включені у результат
    expect($allUsers)->toHaveCount(2)
        ->and($allUsers->contains($bannedUser))->toBeTrue()
        ->and($allUsers->contains($activeUser))->toBeTrue();
});
