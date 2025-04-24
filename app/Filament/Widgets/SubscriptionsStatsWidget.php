<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Tariff;
use App\Models\User;
use App\Models\UserSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriptionsStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make(__('Користувачі'), User::count())
                ->description(__('Загальна кількість користувачів'))
                ->descriptionIcon('heroicon-m-user')
                ->color('primary')
                ->chart(User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),

            Stat::make(__('Активні підписки'), UserSubscription::where('is_active', true)->count())
                ->description(__('Кількість активних підписок'))
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success')
                ->chart(UserSubscription::where('is_active', true)
                    ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),

            Stat::make(__('Закінчуються підписки'), 
                UserSubscription::where('is_active', true)
                    ->where('end_date', '<=', now()->addDays(7))
                    ->where('end_date', '>=', now())
                    ->count())
                ->description(__('Закінчуються протягом 7 днів'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Платежі'), Payment::count())
                ->description(__('Загальна кількість платежів'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info')
                ->chart(Payment::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('count')
                    ->toArray()),

            Stat::make(__('Дохід'), Payment::sum('amount') . ' UAH')
                ->description(__('Загальна сума платежів'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart(Payment::selectRaw('DATE(created_at) as date, SUM(amount) as sum')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->groupBy('date')
                    ->pluck('sum')
                    ->toArray()),
        ];
    }
}
