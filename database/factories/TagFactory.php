<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->word().' '.Str::substr(Str::uuid(), 0, 6);
        $description = fake()->sentence(10);

        return [
            'slug' => Tag::generateSlug($name),
            'name' => $name,
            'description' => $description,
            'image' => fake()->boolean(50) ? fake()->imageUrl(640, 480, 'tags') : null,
            'aliases' => fake()->words(rand(0, 10)),
            'is_genre' => fake()->boolean(20),
            'meta_title' => fake()->boolean(70) ? fake()->words(3,
                    true).'| '.config('app.name') : "$name | ".config('app.name'),
            'meta_description' => fake()->boolean(70) ? fake()->sentence(15) : $description,
            'meta_image' => fake()->boolean(50) ? fake()->imageUrl(640, 480, 'tags-meta') : null,
        ];
    }
}
