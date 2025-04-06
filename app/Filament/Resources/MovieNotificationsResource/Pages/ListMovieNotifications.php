<?php

namespace App\Filament\Resources\MovieNotificationsResource\Pages;

use App\Filament\Resources\MovieNotificationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMovieNotifications extends ListRecords
{
    protected static string $resource = MovieNotificationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
