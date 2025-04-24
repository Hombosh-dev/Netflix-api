<?php

namespace App\Filament\Resources\SelectionResource\Pages;

use App\Filament\Resources\SelectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSelection extends EditRecord
{
    protected static string $resource = SelectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(__('Переглянути')),

            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення підбірки'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цю підбірку?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
