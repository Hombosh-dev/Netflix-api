<?php

namespace Database\Seeders;

use App\Enums\PersonType;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run(): void
    {
        // Create 40 movies
        $movies = Movie::factory(40)->create();

        // Get all tags
        $tags = Tag::all();

        // Get all persons
        $persons = Person::all();
        $actors = $persons->where('type', PersonType::ACTOR)->values();
        $directors = $persons->where('type', PersonType::DIRECTOR)->values();
        $voiceActors = $persons->where('type', PersonType::VOICE_ACTOR)->values();

        // Attach random tags and persons to each movie
        foreach ($movies as $movie) {
            // Attach random tags (between 2 and 6)
            $randomTags = $tags->random(rand(2, 6));
            $movie->tags()->attach($randomTags);

            // Attach random actors (between 2 and 8)
            $actorCount = min(rand(2, 8), $actors->count());
            for ($i = 0; $i < $actorCount; $i++) {
                if ($i < $actors->count()) {
                    $actor = $actors[$i];
                    $pivotData = [
                        'character_name' => "Character " . ($i + 1)
                    ];

                    // For some actors, add a voice actor
                    if ($i < 2 && $voiceActors->count() > 0) {
                        $pivotData['voice_person_id'] = $voiceActors->random()->id;
                    }

                    $movie->persons()->attach($actor->id, $pivotData);
                }
            }

            // Attach a director if available
            if ($directors->count() > 0) {
                $director = $directors->random();
                $movie->persons()->attach($director->id, [
                    'character_name' => 'Director'
                ]);
            }
        }
    }
}
