<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\User;
use App\Models\Tag;
use App\Models\Movie;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can delete a tag', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a tag
    $tag = Tag::factory()->create([
        'name' => 'Deletable Tag',
        'slug' => 'deletable-tag'
    ]);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/tags/{$tag->slug}");
    
    // Assert
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Tag deleted successfully'
        ]);
    
    // Check that the tag was deleted
    $this->assertDatabaseMissing('tags', [
        'id' => $tag->id
    ]);
});

test('admin cannot delete a tag with associated movies', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Create a tag
    $tag = Tag::factory()->create([
        'name' => 'Tag with Movies',
        'slug' => 'tag-with-movies'
    ]);
    
    // Create a movie and associate it with the tag
    $movie = Movie::factory()->create();
    $tag->movies()->attach($movie->id);
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/tags/{$tag->slug}");
    
    // Assert
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Cannot delete tag with associated movies. Remove associations first.'
        ]);
    
    // Check that the tag was not deleted
    $this->assertDatabaseHas('tags', [
        'id' => $tag->id
    ]);
});

test('non-admin cannot delete a tag', function () {
    // Arrange
    $user = User::factory()->create();
    
    $tag = Tag::factory()->create([
        'name' => 'Regular Tag',
        'slug' => 'regular-tag'
    ]);
    
    // Act
    $response = $this->actingAs($user)
        ->deleteJson("/api/v1/admin/tags/{$tag->slug}");
    
    // Assert
    $response->assertStatus(403);
    
    // Check that the tag was not deleted
    $this->assertDatabaseHas('tags', [
        'id' => $tag->id
    ]);
});

test('unauthenticated user cannot delete a tag', function () {
    // Arrange
    $tag = Tag::factory()->create([
        'name' => 'Regular Tag',
        'slug' => 'regular-tag'
    ]);
    
    // Act
    $response = $this->deleteJson("/api/v1/admin/tags/{$tag->slug}");
    
    // Assert
    $response->assertStatus(401);
    
    // Check that the tag was not deleted
    $this->assertDatabaseHas('tags', [
        'id' => $tag->id
    ]);
});

test('returns 404 when trying to delete non-existent tag', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    
    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });
    
    // Generate a non-existent slug
    $nonExistentSlug = 'non-existent-tag';
    
    // Act
    $response = $this->actingAs($admin)
        ->deleteJson("/api/v1/admin/tags/{$nonExistentSlug}");
    
    // Assert
    $response->assertStatus(404);
});
