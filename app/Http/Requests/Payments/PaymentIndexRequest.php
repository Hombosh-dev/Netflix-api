<?php

namespace App\Http\Requests\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PaymentIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Payment::class);
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
            'tariff_id' => ['sometimes', 'string', 'exists:tariffs,id'],
            'status' => ['sometimes', 'string', new Enum(PaymentStatus::class)],
            'payment_method' => ['sometimes', 'string', 'max:50'],
            'min_amount' => ['sometimes', 'numeric', 'min:0'],
            'max_amount' => ['sometimes', 'numeric', 'min:0', 'gte:min_amount'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', Rule::in(['created_at', 'updated_at', 'amount'])],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    /**
     * Get the query parameters for the request.
     *
     * @return array
     */
    public function queryParameters()
    {
        return [
            'user_id' => [
                'description' => 'ID користувача для фільтрації платежів.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'tariff_id' => [
                'description' => 'ID тарифу для фільтрації платежів.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'status' => [
                'description' => 'Статус платежу для фільтрації.',
                'example' => 'COMPLETED',
            ],
            'payment_method' => [
                'description' => 'Метод оплати для фільтрації.',
                'example' => 'credit_card',
            ],
            'min_amount' => [
                'description' => 'Мінімальна сума платежу для фільтрації.',
                'example' => 100,
            ],
            'max_amount' => [
                'description' => 'Максимальна сума платежу для фільтрації.',
                'example' => 500,
            ],
            'currency' => [
                'description' => 'Валюта платежу для фільтрації.',
                'example' => 'UAH',
            ],
            'date_from' => [
                'description' => 'Початкова дата для фільтрації платежів.',
                'example' => '2023-01-01',
            ],
            'date_to' => [
                'description' => 'Кінцева дата для фільтрації платежів.',
                'example' => '2023-12-31',
            ],
            'page' => [
                'description' => 'Номер сторінки для пагінації.',
                'example' => 1,
            ],
            'per_page' => [
                'description' => 'Кількість елементів на сторінці.',
                'example' => 15,
            ],
            'sort' => [
                'description' => 'Поле для сортування результатів.',
                'example' => 'amount',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc або desc).',
                'example' => 'desc',
            ],
        ];
    }
}
