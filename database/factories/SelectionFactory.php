<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Selection>
 */
class SelectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence;

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'slug' => $name,
            'name' => $name,
            'description' => $this->faker->optional()->paragraph,
            'meta_title' => $this->faker->optional()->sentence,
            'meta_description' => $this->faker->optional()->text(376),
            'meta_image' => $this->faker->imageUrl(2048, 2048),
        ];
    }
}
