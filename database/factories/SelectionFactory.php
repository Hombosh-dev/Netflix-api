<?php

namespace Database\Factories;

use App\Models\Selection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Selection>
 */
class SelectionFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->sentence;

        return [
            'user_id' => User::inRandomOrder()->value('id') ?? User::factory(),
            'slug' => Selection::generateSlug($name),
            'name' => $name,
            'description' => fake()->optional()->paragraph,
            'meta_title' => Selection::makeMetaTitle($name),
            'meta_description' => fake()->optional()->text(376),
            'meta_image' => fake()->imageUrl(2048, 2048),
        ];
    }
}
