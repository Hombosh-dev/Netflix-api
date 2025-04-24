<?php

namespace Database\Seeders;

use App\Enums\UserListType;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserListSeeder extends Seeder
{
    public function run(): void
    {
        // All users
        $users = User::all();

        foreach ($users as $user) {
            // Favorite movies
            $favoriteMovies = Movie::inRandomOrder()->take(rand(5, 15))->get();
            foreach ($favoriteMovies as $movie) {
                $user->userLists()->create([
                    'listable_id' => $movie->id,
                    'listable_type' => Movie::class,
                    'type' => UserListType::FAVORITE->value,
                ]);
            }

            // Favorite people
            $favoritePeople = Person::inRandomOrder()->take(rand(5, 15))->get();
            foreach ($favoritePeople as $person) {
                $user->userLists()->create([
                    'listable_id' => $person->id,
                    'listable_type' => Person::class,
                    'type' => UserListType::FAVORITE->value,
                ]);
            }

            // Favorite tags
            $favoriteTags = Tag::inRandomOrder()->take(rand(5, 15))->get();
            foreach ($favoriteTags as $tag) {
                $user->userLists()->create([
                    'listable_id' => $tag->id,
                    'listable_type' => Tag::class,
                    'type' => UserListType::FAVORITE->value,
                ]);
            }

            // Watching episodes
            $watchingEpisodes = Episode::inRandomOrder()->take(rand(5, 15))->get();
            foreach ($watchingEpisodes as $episode) {
                $user->userLists()->create([
                    'listable_id' => $episode->id,
                    'listable_type' => Episode::class,
                    'type' => UserListType::WATCHING->value,
                ]);
            }
        }
    }
}
