<?php

namespace App\Console\Commands;

use App\Enums\Kind;
use App\Models\Movie;
use Illuminate\Console\Command;

class CheckEpisodesCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:episodes-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the number of episodes for each TV series';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Перевірка кількості епізодів у серіалах...');
        
        // Отримуємо всі серіали
        $series = Movie::whereIn('kind', [Kind::TV_SERIES, Kind::ANIMATED_SERIES])->get();
        
        if ($series->isEmpty()) {
            $this->info('Серіалів не знайдено.');
            return Command::SUCCESS;
        }
        
        $this->info('Знайдено ' . $series->count() . ' серіалів.');
        
        // Створюємо масив для зберігання результатів
        $results = [];
        
        // Підраховуємо кількість епізодів для кожного серіалу
        foreach ($series as $show) {
            $count = $show->episodes()->count();
            $results[] = [
                'id' => $show->id,
                'name' => $show->name,
                'kind' => $show->kind->value,
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
        $this->info('Середня кількість епізодів на серіал: ' . $avgEpisodes);
        $this->info('Мінімальна кількість епізодів: ' . $minEpisodes);
        $this->info('Максимальна кількість епізодів: ' . $maxEpisodes);
        
        // Перевіряємо, чи є серіали з малою кількістю епізодів
        $lowEpisodesSeries = array_filter($results, function($item) {
            return $item['episodes'] < 6;
        });
        
        if (!empty($lowEpisodesSeries)) {
            $this->warn('Знайдено ' . count($lowEpisodesSeries) . ' серіалів з менше ніж 6 епізодами:');
            foreach ($lowEpisodesSeries as $item) {
                $this->warn("- {$item['name']}: {$item['episodes']} епізодів");
            }
        } else {
            $this->info('Всі серіали мають не менше 6 епізодів.');
        }
        
        return Command::SUCCESS;
    }
}
