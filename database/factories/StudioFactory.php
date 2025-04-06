<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Studio>
 */
class StudioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $company = $this->faker->unique()->company();

        return [
            'slug' => $company,
            'name' => $company,
            'description' => $this->faker->paragraph(),
            'image' => $this->faker->imageUrl(),
            'meta_title' => $this->faker->sentence(),
            'meta_description' => $this->faker->sentence(),
            'meta_image' => $this->faker->imageUrl(),
        ];
    }
}
