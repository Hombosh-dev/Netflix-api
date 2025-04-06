<?php

namespace App\Filament\Resources\RatingsResource\Pages;

use App\Filament\Resources\RatingsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRatings extends EditRecord
{
    protected static string $resource = RatingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
