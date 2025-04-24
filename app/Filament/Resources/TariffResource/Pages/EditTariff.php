<?php

namespace App\Filament\Resources\TariffResource\Pages;

use App\Filament\Resources\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTariff extends EditRecord
{
    protected static string $resource = TariffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(__('Переглянути')),

            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення тарифу'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цей тариф?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
