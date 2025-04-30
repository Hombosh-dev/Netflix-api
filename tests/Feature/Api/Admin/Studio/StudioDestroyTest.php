<?php

namespace Tests\Feature\Api\Admin\Studio;

use App\Models\User;
use App\Models\Studio;
use App\Models\Movie;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can delete a studio', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a studio
    $studio = Studio::factory()->create([
        'name' => 'Deletable Studio',
        'slug' => 'deletable-studio'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/studios/{$studio->slug}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Studio deleted successfully'
        ]);
    
    // Check that the studio was deleted
    $this->assertDatabaseMissing('studios', [
        'id' => $studio->id
    ]);
});

test('admin cannot delete a studio with associated movies', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a studio
    $studio = Studio::factory()->create([
        'name' => 'Studio with Movies',
        'slug' => 'studio-with-movies'
    ]);
    
    // Create a movie and associate it with the studio
    $movie = Movie::factory()->create([
        'studio_id' => $studio->id
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/studios/{$studio->slug}");
    
    // Assert
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Cannot delete studio with associated movies. Remove associations first.'
        ]);
    
    // Check that the studio was not deleted
    $this->assertDatabaseHas('studios', [
        'id' => $studio->id
    ]);
});

test('non-admin cannot delete a studio', function () {
    // Arrange
    $user = User::factory()->create();
    
    $studio = Studio::factory()->create([
        'name' => 'Regular Studio',
        'slug' => 'regular-studio'
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/admin/studios/{$studio->slug}");
    
    // Assert
    $response->assertStatus(403);
    
    // Check that the studio was not deleted
    $this->assertDatabaseHas('studios', [
        'id' => $studio->id
    ]);
});

test('unauthenticated user cannot delete a studio', function () {
    // Arrange
    $studio = Studio::factory()->create([
        'name' => 'Regular Studio',
        'slug' => 'regular-studio'
    ]);
    
    // Act
    $response = $this->deleteJson("/api/v1/admin/studios/{$studio->slug}");
    
    // Assert
    $response->assertStatus(401);
    
    // Check that the studio was not deleted
    $this->assertDatabaseHas('studios', [
        'id' => $studio->id
    ]);
});

test('returns 404 when trying to delete non-existent studio', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Generate a non-existent slug
    $nonExistentSlug = 'non-existent-studio';
    
    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/studios/{$nonExistentSlug}");
    
    // Assert
    $response->assertStatus(404);
});
