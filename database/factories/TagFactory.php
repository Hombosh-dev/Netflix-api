<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * List of common movie genres and tags for more realistic data
     */
    private array $movieTags = [
        // Genres
        'Action', 'Adventure', 'Animation', 'Comedy', 'Crime', 'Documentary', 'Drama', 'Family',
        'Fantasy', 'History', 'Horror', 'Music', 'Mystery', 'Romance', 'Science Fiction',
        'Thriller', 'War', 'Western', 'Superhero', 'Anime', 'Martial Arts', 'Musical',
        
        // Content tags
        'Based on Book', 'Based on True Story', 'Cult Classic', 'Award Winning', 'Indie',
        'Blockbuster', 'Psychological', 'Dystopian', 'Post-Apocalyptic', 'Time Travel',
        'Supernatural', 'Coming of Age', 'Heist', 'Sports', 'Political', 'Spy', 'Biographical',
        'Period Drama', 'Noir', 'Parody', 'Satire', 'Anthology', 'Experimental', 'Surreal',
        
        // Mood tags
        'Inspirational', 'Feel-Good', 'Dark', 'Suspenseful', 'Funny', 'Heartwarming', 'Emotional',
        'Thought-Provoking', 'Intense', 'Scary', 'Uplifting', 'Nostalgic', 'Quirky', 'Violent',
    ];
    
    public function definition(): array
    {
        // 80% chance to use a predefined tag, 20% chance to generate a random one
        $useRealTag = fake()->boolean(80);
        
        if ($useRealTag) {
            $name = fake()->randomElement($this->movieTags);
            // Add a random suffix to avoid unique constraint violations when seeding multiple times
            if (fake()->boolean(30)) {
                $name .= ' ' . fake()->randomNumber(3, true);
            }
        } else {
            $name = fake()->word() . ' ' . Str::substr(Str::uuid(), 0, 6);
        }
        
        $description = fake()->sentence(10);
        $isGenre = $useRealTag && in_array($name, array_slice($this->movieTags, 0, 23)) && fake()->boolean(80);

        return [
            'slug' => Tag::generateSlug($name),
            'name' => $name,
            'description' => $description,
            'image' => fake()->optional(0.7)->imageUrl(640, 480, 'tags'), // 70% chance to have image
            'aliases' => json_encode(fake()->boolean(60) ? fake()->words(rand(1, 5)) : []),
            'is_genre' => $isGenre, // Likely to be a genre if it's from the genres section
            'meta_title' => "$name | " . config('app.name'),
            'meta_description' => fake()->boolean(70) ? fake()->sentence(15) : $description, // 70% chance for unique description
            'meta_image' => fake()->optional(0.6)->imageUrl(1200, 630, 'tags-meta'), // 60% chance to have meta image
        ];
    }
    
    /**
     * Configure the tag as a genre.
     */
    public function asGenre(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_genre' => true,
        ]);
    }
    
    /**
     * Configure the tag with a specific name.
     */
    public function named(string $name): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'slug' => Tag::generateSlug($name),
            'meta_title' => "$name | " . config('app.name'),
        ]);
    }
}
