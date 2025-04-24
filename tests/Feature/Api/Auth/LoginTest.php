<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users can login through api', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertNoContent();
    $this->assertAuthenticated();
});

test('users cannot login with incorrect password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    $this->assertGuest();
});

test('users cannot login with non-existent email', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    $this->assertGuest();
});

test('users are rate limited after too many attempts', function () {
    // Create a user
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    // Make 6 failed login attempts (Laravel's default is 5 attempts)
    for ($i = 0; $i < 6; $i++) {
        $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
    }

    // The 6th attempt should be rate limited
    $response = $this->postJson('/api/v1/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    // Перевіряємо, що повідомлення містить текст про обмеження спроб входу
    $responseData = $response->json();
    expect($responseData['errors']['email'][0])->toContain('Забагато спроб входу');

    $this->assertGuest();
});
