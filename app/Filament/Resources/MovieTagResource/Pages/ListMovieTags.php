<?php

namespace App\Filament\Resources\MovieTagResource\Pages;

use App\Filament\Resources\MovieTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMovieTags extends ListRecords
{
    protected static string $resource = MovieTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
