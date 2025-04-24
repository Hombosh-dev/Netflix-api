<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('users can request password reset link', function () {
    Notification::fake();
    
    $user = User::factory()->create();
    
    $response = $this->postJson('/api/v1/forgot-password', [
        'email' => $user->email,
    ]);
    
    $response->assertStatus(200)
        ->assertJson(['status' => __('passwords.sent')]);
    
    Notification::assertSentTo($user, ResetPassword::class);
});

test('users cannot request password reset for non-existent email', function () {
    $response = $this->postJson('/api/v1/forgot-password', [
        'email' => 'nonexistent@example.com',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('users can reset password with valid token', function () {
    $user = User::factory()->create();
    
    $token = Password::createToken($user);
    
    $response = $this->postJson('/api/v1/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);
    
    $response->assertStatus(200)
        ->assertJson(['status' => __('passwords.reset')]);
    
    // Verify the password was updated
    $user->refresh();
    expect(Hash::check('new-password', $user->password))->toBeTrue();
});

test('users cannot reset password with invalid token', function () {
    $user = User::factory()->create([
        'password' => bcrypt('old-password'),
    ]);
    
    $response = $this->postJson('/api/v1/reset-password', [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);
    
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
    
    // Verify the password was not updated
    $user->refresh();
    expect(Hash::check('old-password', $user->password))->toBeTrue();
});
