<?php

use App\Actions\Auth\SendPasswordResetLink;
use App\DTOs\Auth\PasswordResetLinkDTO;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('it sends password reset link to existing user', function () {
    // Arrange
    Notification::fake();
    
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);
    
    // Make sure the user exists in the database
    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    
    $action = new SendPasswordResetLink();
    $dto = new PasswordResetLinkDTO(
        email: 'test@example.com',
    );

    // Act
    $status = $action->handle($dto);

    // Assert
    expect($status)->toBe(Password::RESET_LINK_SENT);
    
    Notification::assertSentTo($user, ResetPassword::class);
});

test('it throws exception for non existing user', function () {
    // Arrange
    $action = new SendPasswordResetLink();
    $dto = new PasswordResetLinkDTO(
        email: 'nonexistent@example.com',
    );

    // Act & Assert
    expect(fn () => $action->handle($dto))
        ->toThrow(ValidationException::class);
});
