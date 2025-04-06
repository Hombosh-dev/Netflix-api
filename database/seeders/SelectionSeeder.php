<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\People;
use App\Models\Selection;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SelectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $selections = Selection::factory(20)->create();

        $selections->each(function (Selection $selection) {
            $movies = Movie::inRandomOrder()->take(rand(5, 10))->pluck('id');
            $persons = People::inRandomOrder()->take(rand(5, 10))->pluck('id');

            $selection->movies()->attach($movies);
            $selection->persons()->attach($persons);
        });
    }
}
