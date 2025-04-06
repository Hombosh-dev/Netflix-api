<?php

namespace App\Filament\Resources\RatingsResource\Pages;

use App\Filament\Resources\RatingsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRatings extends ListRecords
{
    protected static string $resource = RatingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
