<?php

namespace App\Filament\Resources\CommentLikeResource\Pages;

use App\Filament\Resources\CommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommentLikes extends ListRecords
{
    protected static string $resource = CommentLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label(__('Створити')),
        ];
    }
}
