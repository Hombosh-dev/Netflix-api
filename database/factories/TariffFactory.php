<?php

namespace Database\Factories;

use App\Models\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tariff>
 */
class TariffFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->randomElement(['Basic', 'Standard', 'Premium', 'Ultra', 'Family', 'Student']);
        $name .= ' ' . fake()->randomElement(['Plan', 'Tariff', 'Subscription']);

        $features = [];
        if (fake()->boolean(80)) {
            $features[] = 'HD quality';
        }
        if (fake()->boolean(50)) {
            $features[] = '4K quality';
        }
        if (fake()->boolean(70)) {
            $features[] = 'Ad-free';
        }
        if (fake()->boolean(60)) {
            $features[] = 'Download for offline viewing';
        }
        if (fake()->boolean(40)) {
            $features[] = 'Multiple device streaming';
        }
        if (fake()->boolean(30)) {
            $features[] = 'Exclusive content';
        }

        return [
            'name' => $name,
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(2, 49, 499),
            'currency' => fake()->randomElement(['UAH', 'USD', 'EUR']),
            'duration_days' => fake()->randomElement([7, 30, 90, 180, 365]),
            'features' => $features,
            'is_active' => fake()->boolean(90), // 90% chance to be active
            'slug' => Tariff::generateSlug($name),
            'meta_title' => Tariff::makeMetaTitle($name),
            'meta_description' => fake()->sentence(10),
            'meta_image' => fake()->optional(0.7)->imageUrl(1200, 630),
        ];
    }

    /**
     * Configure the tariff as inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
