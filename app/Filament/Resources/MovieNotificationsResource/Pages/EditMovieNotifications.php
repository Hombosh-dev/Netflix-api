<?php

namespace App\Filament\Resources\MovieNotificationsResource\Pages;

use App\Filament\Resources\MovieNotificationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMovieNotifications extends EditRecord
{
    protected static string $resource = MovieNotificationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
