<?php

namespace Database\Factories;

use App\Models\Studio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Studio>
 */
class StudioFactory extends Factory
{
    public function definition(): array
    {
        $company = fake()->unique()->company();

        return [
            'slug' => Studio::generateSlug($company),
            'name' => $company,
            'description' => fake()->paragraph(),
            'image' => fake()->imageUrl(),
            'meta_title' => Studio::makeMetaTitle($company),
            'meta_description' => fake()->sentence(),
            'meta_image' => fake()->imageUrl(),
        ];
    }
}
