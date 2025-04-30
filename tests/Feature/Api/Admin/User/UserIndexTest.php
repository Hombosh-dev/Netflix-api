<?php

namespace Tests\Feature\Api\Admin\User;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can view all users', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create some users
    User::factory()->count(5)->create();

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/users');

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'role',
                    'created_at',
                    'updated_at'
                ]
            ],
            'links',
            'meta'
        ]);

    // Check that we have users in the response
    $this->assertNotEmpty($response->json('data'));
});

test('admin can filter users by role', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create users with different roles
    User::factory()->count(3)->create(['role' => Role::USER->value]);
    User::factory()->count(2)->create(['role' => Role::MODERATOR->value]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act - filter by moderator role
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/users?roles[]=moderator');

    // Assert
    $response->assertStatus(200);

    // Check that we only have moderators in the response
    $users = $response->json('data');
    foreach ($users as $user) {
        $this->assertEquals('moderator', $user['role']);
    }

    // Check that we have moderators in the response
    $this->assertNotEmpty($users);
});

test('admin can search users by name or email', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create users with specific names for testing search
    User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com'
    ]);

    User::factory()->create([
        'name' => 'Jane Smith',
        'email' => 'jane@example.com'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act - search by name
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/users?q=John');

    // Assert
    $response->assertStatus(200);

    // Check that we found John Doe
    $users = $response->json('data');
    $this->assertCount(1, $users);
    $this->assertEquals('John Doe', $users[0]['name']);

    // Act - search by email
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/users?q=jane@example');

    // Assert
    $response->assertStatus(200);

    // Check that we found Jane Smith
    $users = $response->json('data');
    $this->assertCount(1, $users);
    $this->assertEquals('Jane Smith', $users[0]['name']);
});

test('admin can sort users', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create users with specific names for testing sorting
    User::factory()->create(['name' => 'Adam']);
    User::factory()->create(['name' => 'Zack']);
    User::factory()->create(['name' => 'Mike']);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Act - sort by name ascending
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/users?sort=name&direction=asc');

    // Assert
    $response->assertStatus(200);

    // Check that users are sorted by name in ascending order
    $users = $response->json('data');
    $names = array_column($users, 'name');
    $sortedNames = $names;
    sort($sortedNames);

    $this->assertEquals($sortedNames, $names);

    // Act - sort by name descending
    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/users?sort=name&direction=desc');

    // Assert
    $response->assertStatus(200);

    // Check that users are sorted by name in descending order
    $users = $response->json('data');
    $names = array_column($users, 'name');
    $sortedNames = $names;
    rsort($sortedNames);

    $this->assertEquals($sortedNames, $names);
});

test('non-admin cannot view all users', function () {
    // Arrange
    $user = User::factory()->create();

    // Create some users
    User::factory()->count(3)->create();

    // Act
    $response = $this->actingAs($user)
        ->getJson('/api/v1/admin/users');

    // Assert
    $response->assertStatus(403);
});

test('unauthenticated user cannot view all users', function () {
    // Arrange
    User::factory()->count(3)->create();

    // Act
    $response = $this->getJson('/api/v1/admin/users');

    // Assert
    $response->assertStatus(401);
});
