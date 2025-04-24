<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(__('Переглянути')),

            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення користувача'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цього користувача?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
