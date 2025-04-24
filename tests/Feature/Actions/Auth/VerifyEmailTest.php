<?php

use App\Actions\Auth\VerifyEmail;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('it verifies user email', function () {
    // Arrange
    Event::fake();
    
    $user = User::factory()->unverified()->create();
    
    // Create a mock of EmailVerificationRequest
    $request = Mockery::mock(EmailVerificationRequest::class);
    $request->shouldReceive('user')->andReturn($user);
    
    $action = new VerifyEmail();

    // Act
    $result = $action->handle($request);

    // Assert
    expect($result)->toBeTrue();
    
    // Refresh user from database
    $user->refresh();
    
    // Check that email was verified
    expect($user->hasVerifiedEmail())->toBeTrue();
    
    // Check that event was dispatched
    Event::assertDispatched(Verified::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});

test('it returns true for already verified user', function () {
    // Arrange
    Event::fake();
    
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);
    
    // Create a mock of EmailVerificationRequest
    $request = Mockery::mock(EmailVerificationRequest::class);
    $request->shouldReceive('user')->andReturn($user);
    
    $action = new VerifyEmail();

    // Act
    $result = $action->handle($request);

    // Assert
    expect($result)->toBeTrue();
    
    // Check that event was not dispatched
    Event::assertNotDispatched(Verified::class);
});
