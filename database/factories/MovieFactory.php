<?php

namespace Database\Factories;

use App\Enums\ApiSourceName;
use App\Enums\AttachmentType;
use App\Enums\Kind;
use App\Enums\MovieRelateType;
use App\Enums\Status;
use App\Models\Movie;
use App\Models\Studio;
use App\Models\Tag;
use App\ValueObjects\ApiSource;
use App\ValueObjects\Attachment;
use App\ValueObjects\RelatedMovie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->sentence(3);
        $studio = Studio::query()->inRandomOrder()->first() ?? Studio::factory()->create();

        return [
            'api_sources' => json_encode($this->generateApiSources()), // Complex JSON for API sources
            'name' => $name,
            'slug' => Movie::generateSlug($name),
            'description' => fake()->paragraph(),
            'image_name' => fake()->imageUrl(640, 480, 'movies', true, 'Movie Poster'), // Like on Netflix
            'aliases' => json_encode(fake()->words(3)), // Array of strings in JSON
            'kind' => fake()->randomElement(Kind::cases())->value, // Enum Kind
            'status' => fake()->randomElement(Status::cases())->value, // Enum Status
            'studio_id' => $studio->id,
            'poster' => fake()->imageUrl(300, 450, 'movies', true, 'Poster'), // Vertical poster
            'duration' => fake()->numberBetween(60, 180), // Duration in minutes
            'countries' => json_encode($this->generateCountries()), // JSON array of countries
            'episodes_count' => fake()->boolean() ? fake()->numberBetween(1, 50) : null, // For TV series
            'first_air_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'last_air_date' => fake()->boolean() ? fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d') : null,
            'imdb_score' => fake()->randomFloat(2, 1, 10), // Rating from 1 to 10
            'attachments' => json_encode($this->generateAttachments()), // Complex JSON for attachments
            'related' => json_encode($this->generateRelated()), // Complex JSON for related movies
            'similars' => $this->generateSimilars(), // JSON array of similar movies (without self-references)
            'is_published' => fake()->boolean(80), // 80% chance to be published
            'meta_title' => fake()->sentence(5),
            'meta_description' => fake()->text(150),
            'meta_image' => fake()->imageUrl(1200, 630, 'seo', true, 'SEO Image'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Generates an array of ApiSource objects.
     */
    private function generateApiSources(): array
    {
        $sources = fake()->randomElements(ApiSourceName::cases(), fake()->numberBetween(1, 3));
        return array_map(fn($source) => new ApiSource(
            source: $source,
            id: fake()->uuid()
        ), $sources);
    }

    /**
     * Generates a JSON array of countries.
     */
    private function generateCountries(): array
    {
        return fake()->randomElements(
            ['UA', 'US', 'GB', 'JP', 'FR', 'DE', 'KR'],
            fake()->numberBetween(1, 3)
        );
    }

    /**
     * Generates an array of Attachment objects.
     */
    private function generateAttachments(): array
    {
        $attachments = [];
        $count = fake()->numberBetween(0, 3);
        for ($i = 0; $i < $count; $i++) {
            $attachments[] = new Attachment(
                type: fake()->randomElement(AttachmentType::cases()),
                url: fake()->url(),
                title: fake()->sentence(4),
                duration: fake()->numberBetween(30, 300) // Seconds
            );
        }

        return $attachments;
    }

    /**
     * Generates an array of RelatedMovie objects.
     */
    private function generateRelated(): array
    {
        $related = [];
        $count = fake()->numberBetween(0, 2);
        for ($i = 0; $i < $count; $i++) {
            $related[] = new RelatedMovie(
                movie_id: Str::ulid(), // Unique ULID
                type: fake()->randomElement(MovieRelateType::cases()) // Enum MovieRelateType
            );
        }

        return $related;
    }

    /**
     * Generates JSON for similars (without self-references).
     */
    private function generateSimilars(): string
    {
        $similars = [];
        $count = fake()->numberBetween(0, 4);
        for ($i = 0; $i < $count; $i++) {
            $similars[] = Str::ulid(); // Unique ULID
        }

        return json_encode($similars);
    }

    /**
     * Assigns a specific studio to the movie.
     */
    public function forStudio(Studio $studio): self
    {
        return $this->state(fn() => [
            'studio_id' => $studio->id,
        ]);
    }

    /**
     * Sets a specific movie kind.
     */
    public function withKind(Kind $kind): self
    {
        return $this->state(fn() => [
            'kind' => $kind->value,
        ]);
    }

    /**
     * Sets a specific movie status.
     */
    public function withStatus(Status $status): self
    {
        return $this->state(fn() => [
            'status' => $status->value,
        ]);
    }

    /**
     * Sets published status.
     */
    public function published(): self
    {
        return $this->state(fn() => [
            'is_published' => true,
        ]);
    }
}
