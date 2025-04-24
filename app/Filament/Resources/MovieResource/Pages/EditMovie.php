<?php

namespace App\Filament\Resources\MovieResource\Pages;

use App\Filament\Resources\MovieResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMovie extends EditRecord
{
    protected static string $resource = MovieResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(__('Переглянути')),

            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення фільму'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цей фільм?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
