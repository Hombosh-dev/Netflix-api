<?php

namespace App\Filament\Resources\RatingsResource\Pages;

use App\Filament\Resources\RatingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRatings extends EditRecord
{
    protected static string $resource = RatingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
