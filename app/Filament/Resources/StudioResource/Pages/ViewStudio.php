<?php

namespace App\Filament\Resources\StudioResource\Pages;

use App\Filament\Resources\StudioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudio extends ViewRecord
{
    protected static string $resource = StudioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('Редагувати')),
                
            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення студії'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цю студію?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }
}
