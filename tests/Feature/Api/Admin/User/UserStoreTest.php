<?php

namespace Tests\Feature\Api\Admin\User;

use App\Enums\Gender;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create a new user', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::USER->value,
        'gender' => Gender::MALE->value,
        'description' => 'This is a test user',
        'birthday' => '1990-01-01',
        'allow_adult' => true,
        'is_auto_next' => true,
        'is_auto_play' => true,
        'is_auto_skip_intro' => true,
        'is_private_favorites' => false
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/users', $userData);
    
    // Assert
    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'role',
                'gender',
                'description',
                'birthday',
                'allow_adult',
                'is_auto_next',
                'is_auto_play',
                'is_auto_skip_intro',
                'is_private_favorites',
                'created_at',
                'updated_at'
            ]
        ]);
    
    // Check that the user was created in the database
    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'role' => Role::USER->value,
        'gender' => Gender::MALE->value,
        'description' => 'This is a test user',
        'birthday' => '1990-01-01',
        'allow_adult' => true,
        'is_auto_next' => true,
        'is_auto_play' => true,
        'is_auto_skip_intro' => true,
        'is_private_favorites' => false
    ]);
});

test('admin can create a user with minimal data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $userData = [
        'name' => 'Minimal User',
        'email' => 'minimal@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::USER->value
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/users', $userData);
    
    // Assert
    $response->assertStatus(201);
    
    // Check that the user was created in the database
    $this->assertDatabaseHas('users', [
        'name' => 'Minimal User',
        'email' => 'minimal@example.com',
        'role' => Role::USER->value
    ]);
});

test('admin can create another admin user', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $userData = [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::ADMIN->value
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/users', $userData);
    
    // Assert
    $response->assertStatus(201);
    
    // Check that the admin user was created in the database
    $this->assertDatabaseHas('users', [
        'name' => 'New Admin',
        'email' => 'newadmin@example.com',
        'role' => Role::ADMIN->value
    ]);
});

test('validation fails when required fields are missing', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $userData = [
        // Missing required fields
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/users', $userData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
});

test('validation fails when email is not unique', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a user with a specific email
    $existingUser = User::factory()->create([
        'email' => 'existing@example.com'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $userData = [
        'name' => 'Duplicate Email User',
        'email' => 'existing@example.com', // Already exists
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::USER->value
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->postJson('/api/v1/admin/users', $userData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('non-admin cannot create a user', function () {
    // Arrange
    $user = User::factory()->create();
    
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::USER->value
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->postJson('/api/v1/admin/users', $userData);
    
    // Assert
    $response->assertStatus(403);
    
    // Check that the user was not created in the database
    $this->assertDatabaseMissing('users', [
        'email' => 'test@example.com'
    ]);
});

test('unauthenticated user cannot create a user', function () {
    // Arrange
    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => Role::USER->value
    ];
    
    // Act
    $response = $this->postJson('/api/v1/admin/users', $userData);
    
    // Assert
    $response->assertStatus(401);
    
    // Check that the user was not created in the database
    $this->assertDatabaseMissing('users', [
        'email' => 'test@example.com'
    ]);
});
