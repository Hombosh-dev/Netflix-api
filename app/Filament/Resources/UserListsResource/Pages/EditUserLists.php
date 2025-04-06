<?php

namespace App\Filament\Resources\UserListsResource\Pages;

use App\Filament\Resources\UserListsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserLists extends EditRecord
{
    protected static string $resource = UserListsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
