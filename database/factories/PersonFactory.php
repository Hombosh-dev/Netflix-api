<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Enums\PersonType;
use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->name();

        return [
            'slug' => Person::generateSlug($name),
            'name' => $name,
            'original_name' => fake()->optional()->name(),
            'gender' => fake()->randomElement(Gender::cases())->value,
            'image' => fake()->imageUrl(640, 480, 'people'),
            'description' => fake()->sentence(15),
            'birthday' => fake()->optional()->date(),
            'birthplace' => fake()->optional()->city(),
            'meta_title' => fake()->randomElement(PersonType::cases())->value.' '.$name.' | '.config('app.name'),
            'meta_description' => fake()->sentence(15),
            'meta_image' => fake()->imageUrl(640, 480, 'people-meta', true),
            'type' => fake()->randomElement(PersonType::cases())->value,
        ];
    }
}
