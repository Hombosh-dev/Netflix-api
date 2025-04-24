<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

test('users can register through api', function () {
    Event::fake();
    
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNoContent();
    $this->assertAuthenticated();
    
    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    
    Event::assertDispatched(Registered::class);
});

test('users cannot register with invalid data', function () {
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'pass',
        'password_confirmation' => 'different-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
    
    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['name' => 'Test User']);
});

test('users cannot register with existing email', function () {
    // Create a user with the email we'll try to register with
    User::factory()->create(['email' => 'existing@example.com']);
    
    $response = $this->postJson('/api/v1/register', [
        'name' => 'Another User',
        'email' => 'existing@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    
    $this->assertGuest();
});
