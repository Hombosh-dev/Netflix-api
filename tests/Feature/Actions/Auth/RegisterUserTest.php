<?php

use App\Actions\Auth\RegisterUser;
use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('it registers a new user', function () {
    // Arrange
    Event::fake();
    $action = new RegisterUser();
    $dto = new RegisterDTO(
        name: 'Test User',
        email: 'test@example.com',
        password: 'password',
    );

    // Act
    $user = $action->handle($dto);

    // Assert
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com');
    
    // Check that the Registered event was dispatched
    Event::assertDispatched(Registered::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
    
    // Check that the user is logged in
    expect(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});
