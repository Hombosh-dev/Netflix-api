<?php

namespace Database\Seeders;

use App\Enums\Kind;
use App\Models\Episode;
use App\Models\Movie;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EpisodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = Movie::all();

        foreach ($movies as $movie) {
            if ($movie->kind === Kind::MOVIE) {
                // Для фільмів типу Movie завжди один епізод з номером 1
                Episode::factory()
                    ->forMovie($movie)
                    ->create(['number' => 1]);
            } elseif ($movie->kind === Kind::TV_SERIES) {
                $episodeCount = rand(2, 10);

                for ($i = 1; $i <= $episodeCount; $i++) {
                    $number = Episode::factory()->generateUniqueNumber($movie->id, false);

                    Episode::factory()
                        ->forMovie($movie)
                        ->create(['number' => $number]);
                }
            }
        }
    }
}
