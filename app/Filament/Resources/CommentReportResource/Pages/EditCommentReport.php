<?php

namespace App\Filament\Resources\CommentReportResource\Pages;

use App\Filament\Resources\CommentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommentReport extends EditRecord
{
    protected static string $resource = CommentReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення скарги'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цю скаргу?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
