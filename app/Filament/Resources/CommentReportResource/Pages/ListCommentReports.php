<?php

namespace App\Filament\Resources\CommentReportResource\Pages;

use App\Filament\Resources\CommentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommentReports extends ListRecords
{
    protected static string $resource = CommentReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Створити')),
        ];
    }
}
