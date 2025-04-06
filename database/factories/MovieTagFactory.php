<?php

namespace Database\Factories;

use App\Models\Movie;
use App\Models\MovieTag;
use App\Models\Tags;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MovieTag>
 */
class MovieTagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'tag_id'   => Tags::factory(),
        ];
    }
}
