<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label(__('Переглянути')),

            Actions\DeleteAction::make()
                ->label(__('Видалити'))
                ->modalHeading(__('Видалення платежу'))
                ->modalDescription(__('Ви впевнені, що хочете видалити цей платіж?'))
                ->modalSubmitActionLabel(__('Так, видалити')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
