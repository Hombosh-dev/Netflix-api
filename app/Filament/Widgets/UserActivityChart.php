<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Rating;
use App\Models\User;
use App\Models\UserList;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class UserActivityChart extends ChartWidget
{
    protected static ?string $heading = 'Активність користувачів';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $days = collect(range(0, 29))->map(function ($daysAgo) {
            return Carbon::now()->subDays($daysAgo)->format('Y-m-d');
        })->reverse()->values();

        $newUsers = $this->getDataForModel(User::class, $days);
        $newComments = $this->getDataForModel(Comment::class, $days);
        $newRatings = $this->getDataForModel(Rating::class, $days);
        $newLists = $this->getDataForModel(UserList::class, $days);

        return [
            'datasets' => [
                [
                    'label' => __('Нові користувачі'),
                    'data' => $newUsers,
                    'borderColor' => '#3b82f6', // blue-500
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => __('Нові коментарі'),
                    'data' => $newComments,
                    'borderColor' => '#ef4444', // red-500
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => __('Нові оцінки'),
                    'data' => $newRatings,
                    'borderColor' => '#10b981', // emerald-500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => __('Нові списки'),
                    'data' => $newLists,
                    'borderColor' => '#f59e0b', // amber-500
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->map(function ($date) {
                return Carbon::parse($date)->format('d.m');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getDataForModel(string $model, $days)
    {
        $counts = $model::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $days->first())
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        return $days->map(function ($date) use ($counts) {
            return $counts[$date] ?? 0;
        })->toArray();
    }
}
