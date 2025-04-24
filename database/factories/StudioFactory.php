<?php

namespace Database\Factories;

use App\Models\Studio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Studio>
 */
class StudioFactory extends Factory
{
    /**
     * List of popular animation studios for more realistic data
     */
    private array $popularStudios = [
        'Netflix Animation',
        'Pixar Animation Studios',
        'Walt Disney Animation Studios',
        'DreamWorks Animation',
        'Studio Ghibli',
        'Illumination Entertainment',
        'Blue Sky Studios',
        'Warner Animation Group',
        'Laika',
        'Aardman Animations',
        'Sony Pictures Animation',
        'Cartoon Saloon',
        'Toei Animation',
        'Madhouse',
        'Kyoto Animation',
        'Production I.G',
        'Bones',
        'Sunrise',
        'A-1 Pictures',
        'Wit Studio',
        'MAPPA',
        'Ufotable',
    ];

    public function definition(): array
    {
        // 70% chance to use a real studio name, 30% chance to generate a random one
        $company = fake()->boolean(70)
            ? fake()->randomElement($this->popularStudios) . ' ' . fake()->randomNumber(4, true)
            : fake()->unique()->company();

        return [
            'slug' => Studio::generateSlug($company),
            'name' => $company,
            'description' => fake()->paragraph(3),
            'image' => fake()->optional(0.9)->imageUrl(400, 400, 'studio'), // 90% chance to have image
            'meta_title' => Studio::makeMetaTitle($company),
            'meta_description' => fake()->sentence(10),
            'meta_image' => fake()->optional(0.8)->imageUrl(1200, 630, 'studio'), // 80% chance to have meta image
            'aliases' => json_encode(fake()->boolean(60) ? fake()->words(rand(1, 3)) : []),
        ];
    }



    /**
     * Configure the studio with a specific name.
     */
    public function named(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'slug' => Studio::generateSlug($name),
            'meta_title' => Studio::makeMetaTitle($name),
        ]);
    }
}
