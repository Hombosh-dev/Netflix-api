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
        $personType = fake()->randomElement(PersonType::cases());        
        
        return [
            'slug' => Person::generateSlug($name),
            'name' => $name,
            'original_name' => fake()->optional(0.7)->name(), // 70% chance to have original name
            'gender' => fake()->boolean(80) ? fake()->randomElement(Gender::cases())->value : null, // 80% chance to have gender
            'image' => fake()->optional(0.9)->imageUrl(640, 480, 'people'), // 90% chance to have image
            'description' => fake()->sentence(15),
            'birthday' => fake()->optional(0.8)->dateTimeBetween('-80 years', '-18 years'), // 80% chance to have birthday
            'birthplace' => fake()->optional(0.7)->city(), // 70% chance to have birthplace
            'meta_title' => $personType->value.' '.$name.' | '.config('app.name'),
            'meta_description' => fake()->sentence(15),
            'meta_image' => fake()->optional(0.8)->imageUrl(640, 480, 'people-meta', true), // 80% chance to have meta image
            'type' => $personType->value,
        ];
    }
}
