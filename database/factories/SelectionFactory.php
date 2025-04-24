<?php

namespace Database\Factories;

use App\Models\Selection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Selection>
 */
class SelectionFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->sentence(3);

        return [
            'user_id' => \App\Models\User::query()->inRandomOrder()->value('id') ?? \App\Models\User::factory(),
            'name' => $name,
            'slug' => Selection::generateSlug($name),
            'description' => fake()->paragraph(),
            'meta_title' => $name . ' | ' . config('app.name'),
            'meta_description' => fake()->sentence(10),
            'meta_image' => fake()->imageUrl(1200, 630, 'selection', true),
        ];
    }


}
