<?php

use App\Actions\Auth\ResendEmailVerification;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('it resends verification email to unverified user', function () {
    // Arrange
    Notification::fake();
    
    $user = User::factory()->unverified()->create();
    
    $action = new ResendEmailVerification();

    // Act
    $result = $action->handle($user);

    // Assert
    expect($result)->toBeTrue();
    
    // Check that notification was sent
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('it returns false for already verified user', function () {
    // Arrange
    Notification::fake();
    
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    
    $action = new ResendEmailVerification();

    // Act
    $result = $action->handle($user);

    // Assert
    expect($result)->toBeFalse();
    
    // Check that notification was not sent
    Notification::assertNotSentTo($user, VerifyEmail::class);
});
