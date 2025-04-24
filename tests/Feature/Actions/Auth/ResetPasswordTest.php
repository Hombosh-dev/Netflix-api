<?php

use App\Actions\Auth\ResetPassword;
use App\DTOs\Auth\PasswordResetDTO;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('it resets user password with valid token', function () {
    // Arrange
    Event::fake();
    
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('old-password'),
    ]);
    
    $token = Password::createToken($user);
    
    $action = new ResetPassword();
    $dto = new PasswordResetDTO(
        email: 'test@example.com',
        password: 'new-password',
        token: $token,
    );

    // Act
    $status = $action->handle($dto);

    // Assert
    expect($status)->toBe(Password::PASSWORD_RESET);
    
    // Refresh user from database
    $user->refresh();
    
    // Check that password was updated
    expect(Hash::check('new-password', $user->password))->toBeTrue();
    
    // Check that event was dispatched
    Event::assertDispatched(PasswordReset::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

test('it throws exception with invalid token', function () {
    // Arrange
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('old-password'),
    ]);
    
    $action = new ResetPassword();
    $dto = new PasswordResetDTO(
        email: 'test@example.com',
        password: 'new-password',
        token: 'invalid-token',
    );

    // Act & Assert
    expect(fn () => $action->handle($dto))
        ->toThrow(ValidationException::class);
});
