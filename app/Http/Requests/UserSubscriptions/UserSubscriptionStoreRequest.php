<?php

namespace App\Http\Requests\UserSubscriptions;

use App\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;

class UserSubscriptionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', UserSubscription::class);
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
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'is_active' => ['sometimes', 'boolean'],
            'auto_renew' => ['sometimes', 'boolean'],
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
                'description' => 'ID користувача, якому призначається підписка (якщо не вказано, використовується поточний користувач).',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'tariff_id' => [
                'description' => 'ID тарифу, який підключається.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'start_date' => [
                'description' => 'Дата початку підписки (якщо не вказано, використовується поточна дата).',
                'example' => '2023-01-01',
            ],
            'end_date' => [
                'description' => 'Дата закінчення підписки (якщо не вказано, розраховується автоматично на основі тривалості тарифу).',
                'example' => '2023-02-01',
            ],
            'is_active' => [
                'description' => 'Чи є підписка активною (за замовчуванням true).',
                'example' => true,
            ],
            'auto_renew' => [
                'description' => 'Чи автоматично продовжувати підписку (за замовчуванням false).',
                'example' => true,
            ],
        ];
    }
}
