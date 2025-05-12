<?php

namespace App\Http\Requests\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PaymentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $payment = $this->route('payment');

        return $this->user()->can('update', $payment);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $payment = $this->route('payment');

        return [
            'tariff_id' => ['sometimes', 'string', 'exists:tariffs,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'payment_method' => ['sometimes', 'string', 'max:50'],
            'transaction_id' => ['sometimes', 'string', 'max:128', Rule::unique('payments', 'transaction_id')->ignore($payment)],
            'status' => ['sometimes', 'string', new Enum(PaymentStatus::class)],
            'liqpay_data' => ['sometimes', 'array'],
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'tariff_id' => [
                'description' => 'ID тарифу, за який здійснюється оплата.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'amount' => [
                'description' => 'Сума платежу.',
                'example' => 199.99,
            ],
            'currency' => [
                'description' => 'Валюта платежу (3 символи).',
                'example' => 'UAH',
            ],
            'payment_method' => [
                'description' => 'Метод оплати.',
                'example' => 'credit_card',
            ],
            'transaction_id' => [
                'description' => 'Унікальний ідентифікатор транзакції.',
                'example' => 'txn_12345678',
            ],
            'status' => [
                'description' => 'Статус платежу (PENDING, COMPLETED, FAILED, тощо).',
                'example' => 'COMPLETED',
            ],
            'liqpay_data' => [
                'description' => 'Додаткові дані від LiqPay.',
                'example' => [
                    'payment_id' => '123456789',
                    'status' => 'success',
                    'paytype' => 'card',
                    'card_token' => 'token123'
                ],
            ],
        ];
    }

    /**
     * Get the URL parameters for the request.
     *
     * @return array
     */
    public function urlParameters()
    {
        return [
            'payment' => [
                'description' => 'ID платежу, який потрібно оновити (ULID).',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
        ];
    }
}
