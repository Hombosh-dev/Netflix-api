<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use Illuminate\Database\Seeder;

// Виправлено з People на Person

class SelectionSeeder extends Seeder
{
    public function run(): void
    {
        // Create 20 selections
        $selections = Selection::factory(20)->create();

        $selections->each(function (Selection $selection) {
            // Choosing unique movies
            $movies = Movie::inRandomOrder()
                ->limit(rand(5, 10))
                ->pluck('id')
                ->toArray();

            // Choosing unique personalities
            $persons = Person::inRandomOrder()
                ->limit(rand(5, 10))
                ->pluck('id')
                ->toArray();

            // Linking movies to a selection
            $selection->movies()->sync($movies);

            // Linking people to a selection
            $selection->persons()->sync($persons);
        });
    }
}
