<?php

namespace App\Filament\Resources\CommentReportResource\Pages;

use App\Filament\Resources\CommentReportResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCommentReport extends CreateRecord
{
    protected static string $resource = CommentReportResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
