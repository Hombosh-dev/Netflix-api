<?php

namespace App\Http\Requests\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PaymentStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Payment::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            'tariff_id' => ['required', 'string', 'exists:tariffs,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'payment_method' => ['required', 'string', 'max:50'],
            'transaction_id' => ['sometimes', 'string', 'max:128', 'unique:payments,transaction_id'],
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
            'user_id' => [
                'description' => 'ID користувача, який здійснює платіж (якщо не вказано, використовується поточний користувач).',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
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
                'description' => 'Унікальний ідентифікатор транзакції (необов’язково).',
                'example' => 'txn_12345678',
            ],
            'status' => [
                'description' => 'Статус платежу (за замовчуванням PENDING).',
                'example' => 'COMPLETED',
            ],
            'liqpay_data' => [
                'description' => 'Додаткові дані від LiqPay (необов’язково).',
                'example' => [
                    'payment_id' => '123456789',
                    'status' => 'success',
                    'paytype' => 'card',
                    'card_token' => 'token123'
                ],
            ],
        ];
    }
}
