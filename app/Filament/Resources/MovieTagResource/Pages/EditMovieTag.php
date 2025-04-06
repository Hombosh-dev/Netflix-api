<?php

namespace App\Filament\Resources\MovieTagResource\Pages;

use App\Filament\Resources\MovieTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMovieTag extends EditRecord
{
    protected static string $resource = MovieTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
