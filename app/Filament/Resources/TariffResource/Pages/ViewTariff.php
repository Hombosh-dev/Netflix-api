<?php

namespace App\Filament\Resources\TariffResource\Pages;

use App\Filament\Resources\TariffResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTariff extends ViewRecord
{
    protected static string $resource = TariffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('Редагувати')),
                
            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення тарифу'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цей тариф?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }
}
