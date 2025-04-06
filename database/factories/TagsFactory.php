<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tags>
 */
class TagsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word().' '.Str::substr(Str::uuid(), 0, 6);
        $description = $this->faker->sentence(10);

        return [
            'slug' => $name,
            'name' => $name,
            'description' => $description,
            'image' => $this->faker->boolean(50) ? $this->faker->imageUrl(640, 480, 'tags') : null,
            'aliases' => $this->faker->words(rand(0, 10)),
            'is_genre' => $this->faker->boolean(20),
            'meta_title' => $this->faker->boolean(70) ? $this->faker->words(3, true).'| '.config('app.name') : "$name | ".config('app.name'),
            'meta_description' => $this->faker->boolean(70) ? $this->faker->sentence(15) : $description,
            'meta_image' => $this->faker->boolean(50) ? $this->faker->imageUrl(640, 480, 'tags-meta') : null,
        ];
    }
}
