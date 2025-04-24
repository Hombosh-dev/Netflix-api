<?php

namespace App\Filament\Resources\TagResource\Pages;

use App\Filament\Resources\TagResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTag extends ViewRecord
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('Редагувати')),
                
            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення тегу'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цей тег?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }
}
