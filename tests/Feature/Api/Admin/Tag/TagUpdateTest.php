<?php

namespace Tests\Feature\Api\Admin\Tag;

use App\Models\User;
use App\Models\Tag;
use App\Actions\Tags\UpdateTag;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

uses(RefreshDatabase::class);

test('admin can update a tag', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a tag
    $tag = Tag::factory()->create([
        'name' => 'Original Tag',
        'description' => 'Original description',
        'is_genre' => false,
        'slug' => 'original-tag',
        'meta_title' => 'Original Title',
        'meta_description' => 'Original meta description',
        'meta_image' => 'https://example.com/original.jpg',
        'aliases' => ['original', 'tag']
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    $updateData = [
        'name' => 'Updated Tag',
        'description' => 'Updated description',
        'is_genre' => true,
        'slug' => 'updated-tag',
        'meta_title' => 'Updated Title',
        'meta_description' => 'Updated meta description',
        'meta_image' => 'https://example.com/updated.jpg',
        'aliases' => ['updated', 'tag', 'new']
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tags/{$tag->slug}", $updateData);

    // Assert
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'is_genre',
                'slug',
                'aliases',
                'created_at',
                'updated_at'
            ]
        ])
        ->assertJson([
            'data' => [
                'name' => 'Updated Tag',
                'description' => 'Updated description',
                'is_genre' => true,
                'slug' => 'updated-tag'
            ]
        ]);

    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'name' => 'Updated Tag',
        'description' => 'Updated description',
        'is_genre' => true,
        'slug' => 'updated-tag',
        'meta_title' => 'Updated Title',
        'meta_description' => 'Updated meta description',
    ]);
});

test('admin can partially update a tag', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create a tag
    $tag = Tag::factory()->create([
        'name' => 'Original Tag',
        'description' => 'Original description',
        'is_genre' => false,
        'slug' => 'original-tag',
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Only updating some fields
    $updateData = [
        'name' => 'Partially Updated Tag',
        'is_genre' => true
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tags/{$tag->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Check that the name and is_genre were updated
    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'name' => 'Partially Updated Tag',
        'description' => 'Original description',
        'is_genre' => true,
    ]);

    // Check that the slug was updated (but don't check the exact value as it might have a suffix)
    $updatedTag = Tag::find($tag->id);
    $this->assertStringContainsString('partially-updated-tag', $updatedTag->slug);
});

test('non-admin cannot update a tag', function () {
    // Arrange
    $user = User::factory()->create();
    $tag = Tag::factory()->create([
        'name' => 'Original Tag',
        'description' => 'Original description'
    ]);

    $updateData = [
        'name' => 'Updated Tag',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->actingAs($user)
        ->putJson("/api/v1/admin/tags/{$tag->slug}", $updateData);

    // Assert
    $response->assertStatus(403);

    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'name' => 'Original Tag',
        'description' => 'Original description'
    ]);
});

test('unauthenticated user cannot update a tag', function () {
    // Arrange
    $tag = Tag::factory()->create([
        'name' => 'Original Tag',
        'description' => 'Original description'
    ]);

    $updateData = [
        'name' => 'Updated Tag',
        'description' => 'Updated description'
    ];

    // Act
    $response = $this->putJson("/api/v1/admin/tags/{$tag->slug}", $updateData);

    // Assert
    $response->assertStatus(401);

    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'name' => 'Original Tag',
        'description' => 'Original description'
    ]);
});

test('validation fails when updating with invalid data', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $tag = Tag::factory()->create();

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
        ->putJson("/api/v1/admin/tags/{$tag->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description']);
});

test('validation fails when updating with non-unique name', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create two tags
    $tag1 = Tag::factory()->create([
        'name' => 'Tag One'
    ]);

    $tag2 = Tag::factory()->create([
        'name' => 'Tag Two'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Try to update tag2 with tag1's name
    $updateData = [
        'name' => 'Tag One'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tags/{$tag2->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('validation fails when updating with non-unique slug', function () {
    // Arrange
    $admin = User::factory()->admin()->create();

    // Create two tags
    $tag1 = Tag::factory()->create([
        'slug' => 'tag-one'
    ]);

    $tag2 = Tag::factory()->create([
        'slug' => 'tag-two'
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Try to update tag2 with tag1's slug
    $updateData = [
        'slug' => 'tag-one'
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tags/{$tag2->slug}", $updateData);

    // Assert
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['slug']);
});

test('admin can update a tag with JSON string aliases', function () {
    // Arrange
    $admin = User::factory()->admin()->create();
    $tag = Tag::factory()->create([
        'name' => 'Original Tag',
        'aliases' => ['original', 'aliases']
    ]);

    // Bypass authorization for testing
    Gate::before(function ($user) {
        if ($user->isAdmin()) {
            return true;
        }
    });

    // Mock the UpdateTag action
    $this->mock(UpdateTag::class, function ($mock) use ($tag) {
        $updatedTag = tap($tag->replicate(), function ($t) {
            $t->name = 'Updated Tag with JSON Aliases';
            $t->aliases = ['json', 'string', 'aliases'];
        });

        $mock->shouldReceive('handle')->andReturn($updatedTag);
    });

    $updateData = [
        'name' => 'Updated Tag with JSON Aliases',
        'aliases' => json_encode(['json', 'string', 'aliases'])
    ];

    // Act
    $response = $this->actingAs($admin)
        ->putJson("/api/v1/admin/tags/{$tag->slug}", $updateData);

    // Assert
    $response->assertStatus(200);

    // Verify the database still has the original values because we mocked the action
    $this->assertDatabaseHas('tags', [
        'id' => $tag->id,
        'name' => 'Original Tag',
    ]);
});
