<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => Movie::factory(),
            'number' => fake()->numberBetween(1, 10),
            'review' => fake()->optional()->text(200),
        ];
    }
}
