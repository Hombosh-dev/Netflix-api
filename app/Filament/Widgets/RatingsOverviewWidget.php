<?php

namespace App\Filament\Widgets;

use App\Models\Rating;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RatingsOverviewWidget extends ChartWidget
{
    protected static ?string $heading = 'Розподіл оцінок';

    protected static ?int $sort = 8;

    protected function getData(): array
    {
        $ratings = Rating::select('number', DB::raw('count(*) as count'))
            ->groupBy('number')
            ->orderBy('number')
            ->get()
            ->pluck('count', 'number')
            ->toArray();

        // Заповнюємо відсутні оцінки нулями
        $allRatings = [];
        for ($i = 1; $i <= 10; $i++) {
            $allRatings[$i] = $ratings[$i] ?? 0;
        }

        $backgroundColor = [
            1 => '#ef4444', // red-500
            2 => '#ef4444', // red-500
            3 => '#f97316', // orange-500
            4 => '#f97316', // orange-500
            5 => '#f59e0b', // amber-500
            6 => '#f59e0b', // amber-500
            7 => '#84cc16', // lime-500
            8 => '#84cc16', // lime-500
            9 => '#10b981', // emerald-500
            10 => '#10b981', // emerald-500
        ];

        return [
            'datasets' => [
                [
                    'label' => __('Кількість оцінок'),
                    'data' => array_values($allRatings),
                    'backgroundColor' => array_values($backgroundColor),
                ],
            ],
            'labels' => array_keys($allRatings),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
