<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $movies = Movie::all();

        // Create a collection to track user-movie combinations
        $userMovieCombinations = collect();

        // Create up to 100 ratings, but ensure unique user-movie combinations
        $count = 0;
        $maxRatings = min(100, $users->count() * $movies->count());

        while ($count < $maxRatings) {
            $user = $users->random();
            $movie = $movies->random();
            $combinationKey = $user->id . '-' . $movie->id;

            // Skip if this combination already exists
            if ($userMovieCombinations->contains($combinationKey)) {
                continue;
            }

            // Add to our tracking collection
            $userMovieCombinations->push($combinationKey);

            // Create the rating
            Rating::factory()
                ->forUserAndMovie($user, $movie)
                ->create();

            $count++;
        }
    }
}
