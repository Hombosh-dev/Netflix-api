<?php

namespace Tests\Feature\Api\Admin\Studio;

use App\Models\User;
use App\Models\Studio;
use App\Actions\Studios\UpdateStudio;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can update a studio', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a studio
    $studio = Studio::factory()->create([
        'name' => 'Original Studio',
        'description' => 'Original description',
        'image' => 'https://example.com/original.jpg',
        'slug' => 'original-studio',
        'meta_title' => 'Original Title',
        'meta_description' => 'Original meta description',
        'meta_image' => 'https://example.com/original-meta.jpg',
        'aliases' => ['original', 'studio']
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $updateData = [
        'name' => 'Updated Studio',
        'description' => 'Updated description',
        'image' => 'https://example.com/updated.jpg',
        'slug' => 'updated-studio',
        'meta_title' => 'Updated Title',
        'meta_description' => 'Updated meta description',
        'meta_image' => 'https://example.com/updated-meta.jpg',
        'aliases' => ['updated', 'studio', 'new']
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/studios/{$studio->slug}", $updateData);
    
    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'image',
                'slug',
                'aliases',
                'created_at',
                'updated_at'
            ]
        ]);
    
    $this->assertDatabaseHas('studios', [
        'id' => $studio->id,
        'name' => 'Updated Studio',
        'description' => 'Updated description',
        'image' => 'https://example.com/updated.jpg',
        'slug' => 'updated-studio',
        'meta_title' => 'Updated Title',
        'meta_description' => 'Updated meta description',
        'meta_image' => 'https://example.com/updated-meta.jpg',
    ]);
    
    // Reload the studio to check aliases
    $studio->refresh();
    $this->assertCount(3, $studio->aliases);
    $this->assertTrue($studio->aliases->contains('updated'));
    $this->assertTrue($studio->aliases->contains('studio'));
    $this->assertTrue($studio->aliases->contains('new'));
});

test('admin can partially update a studio', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a studio
    $studio = Studio::factory()->create([
        'name' => 'Original Studio',
        'description' => 'Original description',
        'image' => 'https://example.com/original.jpg',
        'slug' => 'original-studio',
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Only updating some fields
    $updateData = [
        'name' => 'Partially Updated Studio',
        'description' => 'Updated description'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/studios/{$studio->slug}", $updateData);
    
    // Assert
    $response->assertStatus(200);
    
    // Check that the name and description were updated
    $this->assertDatabaseHas('studios', [
        'id' => $studio->id,
        'name' => 'Partially Updated Studio',
        'description' => 'Updated description',
        'image' => 'https://example.com/original.jpg', // Unchanged
    ]);
    
    // Check that the slug was updated (but don't check the exact value as it might have been auto-generated)
    $updatedStudio = Studio::find($studio->id);
    $this->assertStringContainsString('partially-updated-studio', $updatedStudio->slug);
});

test('non-admin cannot update a studio', function () {
    // Arrange
    $user = User::factory()->create();
    $studio = Studio::factory()->create([
        'name' => 'Original Studio',
        'description' => 'Original description'
    ]);
    
    $updateData = [
        'name' => 'Updated Studio',
        'description' => 'Updated description'
    ];
    
    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/admin/studios/{$studio->slug}", $updateData);
    
    // Assert
    $response->assertStatus(403);
    
    $this->assertDatabaseHas('studios', [
        'id' => $studio->id,
        'name' => 'Original Studio',
        'description' => 'Original description'
    ]);
});

test('unauthenticated user cannot update a studio', function () {
    // Arrange
    $studio = Studio::factory()->create([
        'name' => 'Original Studio',
        'description' => 'Original description'
    ]);
    
    $updateData = [
        'name' => 'Updated Studio',
        'description' => 'Updated description'
    ];
    
    // Act
    $response = $this->putJson("/api/v1/admin/studios/{$studio->slug}", $updateData);
    
    // Assert
    $response->assertStatus(401);
    
    $this->assertDatabaseHas('studios', [
        'id' => $studio->id,
        'name' => 'Original Studio',
        'description' => 'Original description'
    ]);
});

test('validation fails when updating with invalid data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $studio = Studio::factory()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $updateData = [
        'name' => '', // Empty name
        'description' => str_repeat('a', 600) // Too long description
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/studios/{$studio->slug}", $updateData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description']);
});

test('validation fails when updating with non-unique name', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create two studios
    $studio1 = Studio::factory()->create([
        'name' => 'Studio One'
    ]);
    
    $studio2 = Studio::factory()->create([
        'name' => 'Studio Two'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Try to update studio2 with studio1's name
    $updateData = [
        'name' => 'Studio One'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/studios/{$studio2->slug}", $updateData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('validation fails when updating with non-unique slug', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create two studios
    $studio1 = Studio::factory()->create([
        'slug' => 'studio-one'
    ]);
    
    $studio2 = Studio::factory()->create([
        'slug' => 'studio-two'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Try to update studio2 with studio1's slug
    $updateData = [
        'slug' => 'studio-one'
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/studios/{$studio2->slug}", $updateData);
    
    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('admin can update a studio with JSON string aliases', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $studio = Studio::factory()->create([
        'name' => 'Original Studio',
        'aliases' => ['original', 'aliases']
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    $updateData = [
        'name' => 'Updated Studio with JSON Aliases',
        'description' => 'Updated with JSON aliases',
        'aliases' => json_encode(['json', 'string', 'aliases'])
    ];
    
    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/studios/{$studio->slug}", $updateData);
    
    // Assert
    $response->assertStatus(200);
    
    // Get the updated studio and check that aliases were properly decoded
    $studio->refresh();
    $this->assertCount(3, $studio->aliases);
    $this->assertTrue($studio->aliases->contains('json'));
    $this->assertTrue($studio->aliases->contains('string'));
    $this->assertTrue($studio->aliases->contains('aliases'));
});
