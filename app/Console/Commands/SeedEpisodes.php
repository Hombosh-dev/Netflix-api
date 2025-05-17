<?php

namespace App\Console\Commands;

use Database\Seeders\EpisodeSeeder;
use Illuminate\Console\Command;

class SeedEpisodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:episodes {--movies : Seed only movie episodes} {--series : Seed only TV series episodes} {--new : Seed only new series episodes} {--all : Seed all episodes (default)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed episodes for movies and TV series';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to seed episodes...');

        $seeder = new EpisodeSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->info('Episodes seeding completed successfully!');

        return Command::SUCCESS;
    }
}
