<?php

namespace Database\Factories;

use App\Models\Studio;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3);
        $studio = Studio::query()->inRandomOrder()->first() ?? Studio::factory()->create();
        $slug = Str::slug($name) . '-' . random_int(1, 65535);

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => $this->faker->paragraph(),
            'image_name' => $this->faker->imageUrl(640, 480, 'movies', true, 'Movie Poster'),
            'studio_id' => $studio->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Assign a specific studio to the movie.
     */
    public function forStudio(Studio $studio): self
    {
        return $this->state(fn () => [
            'studio_id' => $studio->id,
        ]);
    }
}
