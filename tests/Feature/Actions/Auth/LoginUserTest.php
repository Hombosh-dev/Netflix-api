<?php

use App\Actions\Auth\LoginUser;
use App\DTOs\Auth\LoginDTO;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('it logs in a user with valid credentials', function () {
    // Arrange
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $action = new LoginUser();
    $dto = new LoginDTO(
        email: 'test@example.com',
        password: 'password',
        remember: true,
    );

    // Act
    $result = $action->handle($dto);

    // Assert
    expect($result)->toBeTrue()
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

test('it throws exception with invalid credentials', function () {
    // Arrange
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $action = new LoginUser();
    $dto = new LoginDTO(
        email: 'test@example.com',
        password: 'wrong-password',
        remember: false,
    );

    // Act & Assert
    expect(fn() => $action->handle($dto))
        ->toThrow(ValidationException::class)
        ->and(Auth::check())->toBeFalse();
});

test('it clears rate limiter on successful login', function () {
    // Arrange
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    // Hit the rate limiter once
    $throttleKey = 'test@example.com|127.0.0.1';
    RateLimiter::hit($throttleKey);

    $action = new LoginUser();
    $dto = new LoginDTO(
        email: 'test@example.com',
        password: 'password',
        remember: false,
    );

    // Act
    $action->handle($dto);

    // Assert
    expect(RateLimiter::attempts($throttleKey))->toBe(0);
});
