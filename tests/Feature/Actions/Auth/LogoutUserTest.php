<?php

use App\Actions\Auth\LogoutUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('it logs out a user', function () {
    // Arrange
    $user = User::factory()->create();
    Auth::login($user);
    expect(Auth::check())->toBeTrue();
    
    $action = new LogoutUser();

    // Act
    $action->handle();

    // Assert
    expect(Auth::check())->toBeFalse();
});
