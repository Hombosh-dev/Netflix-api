<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Rating;
use App\Models\Selection;
use App\Models\Tag;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make(__('Фільми'), Movie::count())
                ->description(__('Загальна кількість фільмів'))
                ->descriptionIcon('heroicon-m-film')
                ->color('primary')
                ->chart(Movie::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),

            Stat::make(__('Епізоди'), Episode::count())
                ->description(__('Загальна кількість епізодів'))
                ->descriptionIcon('heroicon-m-video-camera')
                ->color('success')
                ->chart(Episode::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),

            Stat::make(__('Персони'), Person::count())
                ->description(__('Загальна кількість персон'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->chart(Person::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),

            Stat::make(__('Теги'), Tag::count())
                ->description(__('Загальна кількість тегів'))
                ->descriptionIcon('heroicon-m-tag')
                ->color('danger')
                ->chart(Tag::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),

            Stat::make(__('Підбірки'), Selection::count())
                ->description(__('Загальна кількість підбірок'))
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info')
                ->chart(Selection::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),

            Stat::make(__('Коментарі'), Comment::count())
                ->description(__('Загальна кількість коментарів'))
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('gray')
                ->chart(Comment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),
        ];
    }
}
