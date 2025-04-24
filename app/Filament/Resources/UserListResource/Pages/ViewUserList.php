<?php

namespace App\Filament\Resources\UserListResource\Pages;

use App\Filament\Resources\UserListResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserList extends ViewRecord
{
    protected static string $resource = UserListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('Редагувати')),
                
            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення запису'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цей запис?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }
}
