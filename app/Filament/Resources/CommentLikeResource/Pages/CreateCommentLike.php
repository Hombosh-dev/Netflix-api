<?php

namespace App\Filament\Resources\CommentLikeResource\Pages;

use App\Filament\Resources\CommentLikeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCommentLike extends CreateRecord
{
    protected static string $resource = CommentLikeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
