<?php

namespace App\Console\Commands;

use App\Enums\Kind;
use App\Models\Movie;
use Illuminate\Console\Command;

class CheckMovieEpisodesCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:movie-episodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the number of episodes for each movie';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Перевірка кількості епізодів у фільмах...');
        
        // Отримуємо всі фільми (не серіали)
        $movies = Movie::whereIn('kind', [Kind::MOVIE, Kind::ANIMATED_MOVIE])->get();
        
        if ($movies->isEmpty()) {
            $this->info('Фільмів не знайдено.');
            return Command::SUCCESS;
        }
        
        $this->info('Знайдено ' . $movies->count() . ' фільмів.');
        
        // Створюємо масив для зберігання результатів
        $results = [];
        
        // Підраховуємо кількість епізодів для кожного фільму
        foreach ($movies as $movie) {
            $count = $movie->episodes()->count();
            $results[] = [
                'id' => $movie->id,
                'name' => $movie->name,
                'kind' => $movie->kind->value,
                'episodes' => $count
            ];
        }
        
        // Сортуємо результати за кількістю епізодів (від найменшої до найбільшої)
        usort($results, function($a, $b) {
            return $a['episodes'] <=> $b['episodes'];
        });
        
        // Виводимо результати у вигляді таблиці
        $this->table(
            ['ID', 'Назва', 'Тип', 'Кількість епізодів'],
            array_map(function($item) {
                return [
                    $item['id'],
                    $item['name'],
                    $item['kind'],
                    $item['episodes']
                ];
            }, $results)
        );
        
        // Виводимо статистику
        $totalEpisodes = array_sum(array_column($results, 'episodes'));
        $avgEpisodes = round($totalEpisodes / count($results), 2);
        $minEpisodes = min(array_column($results, 'episodes'));
        $maxEpisodes = max(array_column($results, 'episodes'));
        
        $this->info('Загальна кількість епізодів: ' . $totalEpisodes);
        $this->info('Середня кількість епізодів на фільм: ' . $avgEpisodes);
        $this->info('Мінімальна кількість епізодів: ' . $minEpisodes);
        $this->info('Максимальна кількість епізодів: ' . $maxEpisodes);
        
        // Перевіряємо, чи є фільми з неправильною кількістю епізодів
        $wrongEpisodesCount = array_filter($results, function($item) {
            return $item['episodes'] !== 1;
        });
        
        if (!empty($wrongEpisodesCount)) {
            $this->warn('Знайдено ' . count($wrongEpisodesCount) . ' фільмів з кількістю епізодів != 1:');
            foreach ($wrongEpisodesCount as $item) {
                $this->warn("- {$item['name']}: {$item['episodes']} епізодів");
            }
        } else {
            $this->info('Всі фільми мають рівно 1 епізод.');
        }
        
        return Command::SUCCESS;
    }
}
