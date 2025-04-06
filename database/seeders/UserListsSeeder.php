<?php

namespace Database\Seeders;

use App\Enums\UserListType;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\People;
use App\Models\Tags;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserListsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Всі користувачі
        $users = User::all();

        foreach ($users as $user) {
            // Улюблені фільми
            $favoriteMovies = Movie::inRandomOrder()->take(rand(5, 15))->get();
            foreach ($favoriteMovies as $movie) {
                $user->userLists()->create([
                    'listable_id' => $movie->id,
                    'listable_type' => Movie::class,
                    'type' => UserListType::FAVORITE->value,
                ]);
            }

            // Улюблені персони
            $favoritePeople = People::inRandomOrder()->take(rand(5, 15))->get();
            foreach ($favoritePeople as $person) {
                $user->userLists()->create([
                    'listable_id' => $person->id,
                    'listable_type' => People::class,
                    'type' => UserListType::FAVORITE->value,
                ]);
            }

            // Улюблені теги
            $favoriteTags = Tags::inRandomOrder()->take(rand(5, 15))->get();
            foreach ($favoriteTags as $tag) {
                $user->userLists()->create([
                    'listable_id' => $tag->id,
                    'listable_type' => Tags::class,
                    'type' => UserListType::FAVORITE->value,
                ]);
            }

            // Переглядає епізоди
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
