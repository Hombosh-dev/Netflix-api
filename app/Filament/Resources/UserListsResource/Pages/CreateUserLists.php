<?php

namespace App\Filament\Resources\UserListsResource\Pages;

use App\Filament\Resources\UserListResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserLists extends CreateRecord
{
    protected static string $resource = UserListResource::class;
}
