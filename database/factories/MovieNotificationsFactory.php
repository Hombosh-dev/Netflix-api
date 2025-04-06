<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovieNotifications>
 */
class MovieNotificationsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $movie = Movie::inRandomOrder()->first() ?? Movie::factory()->create();
        return [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
        ];
    }

    /**
     * Indicate that the notification is for a specific user.
     *
     * @param User $user
     * @return self
     */
    public function forUser(User $user): self
    {
        return $this->state(fn () => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Indicate that the notification is for a specific movie.
     *
     * @param Movie $movie
     * @return self
     */
    public function forMovie(Movie $movie): self
    {
        return $this->state(fn () => [
            'movie_id' => $movie->id,
        ]);
    }
}
