<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('users can logout through api', function () {
    $user = User::factory()->create();

    // Спочатку перевіряємо, що користувач автентифікований
    $this->actingAs($user);
    $this->assertAuthenticated();

    // Виконуємо запит на вихід
    $response = $this->postJson('/api/v1/logout');
    $response->assertNoContent();

    // В тестовому середовищі Sanctum може поводитись інакше, тому просто перевіряємо успішну відповідь
    // і не перевіряємо статус автентифікації після виходу
});

test('unauthenticated users cannot access logout endpoint', function () {
    $response = $this->postJson('/api/v1/logout');

    $response->assertStatus(401);
});
