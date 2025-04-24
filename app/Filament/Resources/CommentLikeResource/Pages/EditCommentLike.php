<?php

namespace App\Filament\Resources\CommentLikeResource\Pages;

use App\Filament\Resources\CommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommentLike extends EditRecord
{
    protected static string $resource = CommentLikeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення лайку'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цей лайк?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
