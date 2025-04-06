<?php

namespace App\Filament\Resources\UserListsResource\Pages;

use App\Filament\Resources\UserListsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserLists extends ListRecords
{
    protected static string $resource = UserListsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
