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
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'movie_id' => Movie::query()->inRandomOrder()->value('id') ?? Movie::factory(),
            'number' => fake()->numberBetween(1, 10), // Rating from 1 to 10
            'review' => fake()->optional(0.7)->paragraph(2), // 70% chance to have a review
        ];
    }
    
    /**
     * Configure the rating with a specific score.
     */
    public function withScore(int $score): static
    {
        return $this->state(fn (array $attributes) => [
            'number' => $score,
        ]);
    }
    
    /**
     * Configure the rating with a review.
     */
    public function withReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'review' => fake()->paragraph(3),
        ]);
    }
    
    /**
     * Configure the rating without a review.
     */
    public function withoutReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'review' => null,
        ]);
    }
    
    /**
     * Configure the rating for a specific user and movie.
     */
    public function forUserAndMovie(User $user, Movie $movie): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
        ]);
    }
}
