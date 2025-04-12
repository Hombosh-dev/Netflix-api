<?php

namespace Database\Factories;

use App\Enums\ApiSourceName;
use App\Enums\Kind;
use App\Enums\MovieRelateType;
use App\Enums\Status;
use App\Models\Movie;
use App\Models\Studio;
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
            'api_sources' => $this->generateApiSources(), // Складний JSON для API джерел
            'name' => $name,
            'slug' => Movie::generateSlug($name),
            'description' => fake()->paragraph(),
            'image_name' => fake()->imageUrl(640, 480, 'movies', true, 'Movie Poster'), // Як у Netflix
            'aliases' => json_encode(fake()->words(3)), // Масив рядків у JSON
            'kind' => fake()->randomElement(Kind::cases())->value, // Enum Kind
            'status' => fake()->randomElement(Status::cases())->value, // Enum Status
            'studio_id' => $studio->id,
            'poster' => fake()->imageUrl(300, 450, 'movies', true, 'Poster'), // Вертикальний постер
            'duration' => fake()->numberBetween(60, 180), // Тривалість у хвилинах
            'countries' => json_encode($this->generateCountries()), // JSON масив країн
            'episodes_count' => fake()->boolean() ? fake()->numberBetween(1, 50) : null, // Для серіалів
            'first_air_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'last_air_date' => fake()->boolean() ? fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d') : null,
            'imdb_score' => fake()->randomFloat(2, 1, 10), // Оцінка від 1 до 10
            'attachments' => $this->generateAttachments(), // Складний JSON для прикріплень
            'related' => $this->generateRelated(), // Складний JSON для пов’язаних фільмів
            'similars' => $this->generateSimilars(), // JSON схожих фільмів (без самопосилань)
            'is_published' => fake()->boolean(80), // 80% шансів бути опублікованим
            'meta_title' => fake()->sentence(5),
            'meta_description' => fake()->text(150),
            'meta_image' => fake()->imageUrl(1200, 630, 'seo', true, 'SEO Image'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Генерує складний JSON для api_sources.
     */
    private function generateApiSources(): string
    {
        $sources = fake()->randomElements(ApiSourceName::cases(), fake()->numberBetween(1, 3));
        $result = array_map(fn($source) => [
            'source' => $source->value,
            'id' => fake()->uuid(),
        ], $sources);

        return json_encode($result);
    }

    /**
     * Генерує JSON масив країн.
     */
    private function generateCountries(): array
    {
        return fake()->randomElements(
            ['UA', 'US', 'GB', 'JP', 'FR', 'DE', 'KR'],
            fake()->numberBetween(1, 3)
        );
    }

    /**
     * Генерує складний JSON для attachments.
     */
    private function generateAttachments(): string
    {
        $attachments = [];
        $count = fake()->numberBetween(0, 3);
        for ($i = 0; $i < $count; $i++) {
            $attachments[] = [
                'type' => fake()->randomElement(['trailer', 'teaser', 'behind_the_scenes']),
                'url' => fake()->url(),
                'title' => fake()->sentence(4),
                'duration' => fake()->numberBetween(30, 300), // Секунди
            ];
        }

        return json_encode($attachments);
    }

    /**
     * Генерує складний JSON для related.
     */
    private function generateRelated(): string
    {
        $related = [];
        $count = fake()->numberBetween(0, 2);
        for ($i = 0; $i < $count; $i++) {
            $related[] = [
                'movie_id' => Str::ulid(), // Унікальний ULID
                'type' => fake()->randomElement(MovieRelateType::cases())->value, // Enum MovieRelateType
            ];
        }

        return json_encode($related);
    }

    /**
     * Генерує JSON для similars (без самопосилань).
     */
    private function generateSimilars(): string
    {
        $similars = [];
        $count = fake()->numberBetween(0, 4);
        for ($i = 0; $i < $count; $i++) {
            $similars[] = Str::ulid(); // Унікальний ULID
        }

        return json_encode($similars);
    }

    /**
     * Призначає конкретну студію для фільму.
     */
    public function forStudio(Studio $studio): self
    {
        return $this->state(fn() => [
            'studio_id' => $studio->id,
        ]);
    }

    /**
     * Встановлює конкретний тип фільму.
     */
    public function withKind(Kind $kind): self
    {
        return $this->state(fn() => [
            'kind' => $kind->value,
        ]);
    }

    /**
     * Встановлює конкретний статус фільму.
     */
    public function withStatus(Status $status): self
    {
        return $this->state(fn() => [
            'status' => $status->value,
        ]);
    }

    /**
     * Встановлює опублікований статус.
     */
    public function published(): self
    {
        return $this->state(fn() => [
            'is_published' => true,
        ]);
    }
}
