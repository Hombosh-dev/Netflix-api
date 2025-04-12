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
        // Створюємо 20 підбірок
        $selections = Selection::factory(20)->create();

        $selections->each(function (Selection $selection) {
            // Вибираємо унікальні фільми
            $movies = Movie::inRandomOrder()
                ->limit(rand(5, 10))
                ->pluck('id')
                ->toArray();

            // Вибираємо унікальних персон
            $persons = Person::inRandomOrder()
                ->limit(rand(5, 10))
                ->pluck('id')
                ->toArray();

            // Прив’язуємо фільми до підбірки
            $selection->movies()->sync($movies);

            // Прив’язуємо персон до підбірки
            $selection->persons()->sync($persons);
        });
    }
}
