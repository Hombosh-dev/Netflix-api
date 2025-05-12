<?php

namespace Database\Seeders;

use App\Models\Studio;
use Database\Factories\StudioTmdbFactory;
use Illuminate\Database\Seeder;

class StudioTmdbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example studios to fetch from TMDB
        $studioNames = [
            'Netflix Animation',
            'Pixar Animation Studios',
            'Walt Disney Animation Studios',
            'DreamWorks Animation',
            'Studio Ghibli',
        ];

        // Example TMDB IDs for studios
        $studioIds = [
            3166,  // Netflix
            3,     // Pixar
            6125,  // Disney Animation
            521,   // DreamWorks
            10342, // Studio Ghibli
        ];

        // Create studios by name
        foreach ($studioNames as $name) {
            (new StudioTmdbFactory())->fromTmdbByName($name)->create();
        }

        // Create studios by TMDB ID
        foreach ($studioIds as $id) {
            (new StudioTmdbFactory())->fromTmdbById($id)->create();
        }
    }
}
