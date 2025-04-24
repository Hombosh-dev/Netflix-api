<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Перевіряємо, чи є liqpay_data в даних
        if (!isset($data['liqpay_data']) || empty($data['liqpay_data'])) {
            $data['liqpay_data'] = '{}';
        }

        // Перевіряємо, чи є transaction_id в даних
        if (!isset($data['transaction_id']) || empty($data['transaction_id'])) {
            $data['transaction_id'] = (string) Str::uuid();
        }

        return $data;
    }
}
