<?php

namespace App\Filament\Resources\UserListsResource\Pages;

use App\Filament\Resources\UserListsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserLists extends CreateRecord
{
    protected static string $resource = UserListsResource::class;
}
