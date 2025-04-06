<?php

namespace App\Filament\Resources\MovieTagResource\Pages;

use App\Filament\Resources\MovieTagResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMovieTag extends CreateRecord
{
    protected static string $resource = MovieTagResource::class;
}
