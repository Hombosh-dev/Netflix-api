<?php

namespace App\Filament\Widgets;

use App\Models\Movie;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MoviesByKindChart extends ChartWidget
{
    protected static ?string $heading = 'Розподіл фільмів за типами';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Movie::select('kind', DB::raw('count(*) as count'))
            ->groupBy('kind')
            ->get()
            ->mapWithKeys(function ($item) {
                $kindLabel = $item->value ?? 'Невідомо';
                return [$kindLabel => $item->count];
            })
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('Кількість'),
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#f59e0b', // amber-500
                        '#10b981', // emerald-500
                        '#3b82f6', // blue-500
                        '#ef4444', // red-500
                        '#8b5cf6', // violet-500
                        '#ec4899', // pink-500
                        '#6366f1', // indigo-500
                    ],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
