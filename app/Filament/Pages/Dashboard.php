<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ContentStatsWidget;
use App\Filament\Widgets\LatestCommentsWidget;
use App\Filament\Widgets\LatestMoviesWidget;
use App\Filament\Widgets\LatestUsersWidget;
use App\Filament\Widgets\MoviesByKindChart;
use App\Filament\Widgets\RatingsOverviewWidget;
use App\Filament\Widgets\SubscriptionsStatsWidget;
use App\Filament\Widgets\UserActivityChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderWidgets(): array
    {
        return [
            ContentStatsWidget::class,
            SubscriptionsStatsWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            'xl' => 4,
        ];
    }

    public function getWidgets(): array
    {
        return [
            MoviesByKindChart::class,
            UserActivityChart::class,
            RatingsOverviewWidget::class,
            LatestMoviesWidget::class,
            LatestUsersWidget::class,
            LatestCommentsWidget::class,
        ];
    }
}
