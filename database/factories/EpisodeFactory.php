<?php

namespace Database\Factories;

use App\Enums\Kind;
use App\Enums\VideoPlayerName;
use App\Enums\VideoQuality;
use App\Models\Episode;
use App\Models\Movie;
use App\ValueObjects\VideoPlayer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Episode>
 */
class EpisodeFactory extends Factory
{
    public function definition(): array
    {
        $movie = Movie::query()->inRandomOrder()->first() ?? Movie::factory()->create();
        $name = fake()->sentence(3);

        // Get the highest episode number for this movie and increment it
        $maxNumber = Episode::where('movie_id', $movie->id)->max('number') ?? 0;
        $number = $maxNumber + 1;

        return [
            'movie_id' => $movie->id,
            'name' => $name,
            'slug' => Episode::generateSlug($name),
            'description' => fake()->paragraph(),
            'number' => $number,
            'air_date' => fake()->dateTimeBetween('-5 years', '+1 year'),
            'is_filler' => fake()->boolean(20), // 20% chance to be a filler episode
            'pictures' => json_encode($this->generatePictureUrls(rand(1, 3))),
            'video_players' => json_encode($this->generateVideoPlayers()),
            'meta_title' => "E{$number}: {$name} | {$movie->name}",
            'meta_description' => fake()->text(150),
            'meta_image' => fake()->imageUrl(1200, 630, 'episode', true, 'Episode Image'),
        ];
    }

    /**
     * Configure the episode for a specific movie.
     */
    public function forMovie(Movie $movie): self
    {
        return $this->state(fn() => [
            'movie_id' => $movie->id,
        ]);
    }

    /**
     * Configure the episode with a specific number.
     */
    public function withNumber(int $number): self
    {
        return $this->state(fn() => [
            'number' => $number,
        ]);
    }

    /**
     * Generate picture URLs for the episode
     *
     * @param int $count Number of pictures to generate
     * @return array Array of picture URLs
     */
    private function generatePictureUrls(int $count): array
    {
        $pictures = [];
        for ($i = 0; $i < $count; $i++) {
            $pictures[] = fake()->imageUrl(1280, 720, 'episode', true, 'Episode Screenshot');
        }
        return $pictures;
    }

    /**
     * Generate video players for the episode
     *
     * @return array Array of VideoPlayer objects
     */
    private function generateVideoPlayers(): array
    {
        $playerCount = fake()->numberBetween(1, 3);
        $players = [];

        $dubbingOptions = ['Українська', 'Оригінал', 'Багатоголосий', 'Дубляж', 'Субтитри'];
        $localeCodes = ['uk', 'en', 'pl', 'de', 'fr', 'es'];

        for ($i = 0; $i < $playerCount; $i++) {
            $playerName = fake()->randomElement(VideoPlayerName::cases());
            $quality = fake()->randomElement(VideoQuality::cases());

            $players[] = new VideoPlayer(
                name: $playerName,
                url: fake()->url(),
                file_url: fake()->url() . '/video.mp4',
                dubbing: fake()->randomElement($dubbingOptions),
                quality: $quality,
                locale_code: fake()->randomElement($localeCodes)
            );
        }

        return $players;
    }

    /**
     * Generate a unique episode number for a movie
     *
     * @param string $movieId The movie ID
     * @param bool $sequential Whether to generate sequential numbers
     * @return int A unique episode number
     */
    public function generateUniqueNumber(string $movieId, bool $sequential = true): int
    {
        if ($sequential) {
            // Get the highest episode number for this movie
            $maxNumber = Episode::where('movie_id', $movieId)->max('number') ?? 0;
            return $maxNumber + 1;
        } else {
            // Generate a random number that doesn't exist yet
            $existingNumbers = Episode::where('movie_id', $movieId)
                ->pluck('number')
                ->toArray();

            $number = fake()->numberBetween(1, 24);

            // Keep generating until we find a unique number
            while (in_array($number, $existingNumbers)) {
                $number = fake()->numberBetween(1, 24);
            }

            return $number;
        }
    }
}
