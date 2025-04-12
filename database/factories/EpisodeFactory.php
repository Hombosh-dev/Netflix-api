<?php

namespace Database\Factories;

use App\Enums\Kind;
use App\Models\Episode;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Episode>
 */
class EpisodeFactory extends Factory
{
    public function definition(): array
    {
        $movie = Movie::query()->inRandomOrder()->first() ?? Movie::factory();
        $isMovieKind = $movie->kind === Kind::MOVIE; // Перевірка типу Movie

        $name = fake()->sentence(3);

        return [
            'movie_id' => $movie->id,
            'number' => $this->generateUniqueNumber($movie->id, $isMovieKind),
            'slug' => Episode::generateSlug($name),
            'name' => $name,
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(20, 120), // Duration in minutes
            'air_date' => fake()->optional()->dateTimeBetween('-2 years', 'now'),
            'is_filler' => fake()->boolean(10), // 10% chance of being filler
            'pictures' => json_encode($this->generatePictureUrls(rand(1, 3))),
            'video_players' => $this->generateVideoPlayers(),
            'meta_title' => Episode::makeMetaTitle($name),
            'meta_description' => fake()->optional()->sentence(10),
            'meta_image' => fake()->optional()->imageUrl(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function generateUniqueNumber(string $movieId, bool $isMovieKind): int
    {
        if ($isMovieKind) {
            return 1; // Для фільмів завжди номер 1
        }

        // Отримуємо всі існуючі номери для конкретного фільму
        $existingNumbers = collect(Episode::where('movie_id', $movieId)->pluck('number'));

        // Знаходимо наступний доступний номер
        $newNumber = 1;
        while ($existingNumbers->contains($newNumber)) {
            $newNumber++;
        }

        return $newNumber;
    }

    private function generatePictureUrls(int $count): array
    {
        return fake()->randomElements([
            fake()->imageUrl(640, 480, 'movies', true, 'Episode 1'),
            fake()->imageUrl(640, 480, 'movies', true, 'Episode 2'),
            fake()->imageUrl(640, 480, 'movies', true, 'Episode 3'),
        ], $count);
    }

    /**
     * # @return Collection<VideoPlayer>
     */
    private function generateVideoPlayers(): int
    {
        return -1;
        // TODO:
    }

    public function forMovie(Movie $movie): self
    {
        return $this->state(fn() => [
            'movie_id' => $movie->id,
        ]);
    }
}
