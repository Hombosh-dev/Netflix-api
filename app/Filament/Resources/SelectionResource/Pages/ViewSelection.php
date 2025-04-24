<?php

namespace App\Filament\Resources\SelectionResource\Pages;

use App\Filament\Resources\SelectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSelection extends ViewRecord
{
    protected static string $resource = SelectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('Редагувати')),
                
            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення підбірки'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цю підбірку?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }
}
