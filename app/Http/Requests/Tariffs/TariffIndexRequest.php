<?php

namespace App\Http\Requests\Tariffs;

use App\Models\Tariff;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TariffIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0', 'gte:min_price'],
            'duration_days' => ['sometimes', 'integer', 'min:1'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', Rule::in(['created_at', 'updated_at', 'name', 'price', 'duration_days'])],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [];
    }

    /**
     * Get the query parameters for the request.
     *
     * @return array
     */
    public function queryParameters()
    {
        return [
            'q' => [
                'description' => 'Пошуковий запит для фільтрації тарифів.',
                'example' => 'Преміум',
            ],
            'is_active' => [
                'description' => 'Фільтрувати за статусом активності.',
                'example' => true,
            ],
            'currency' => [
                'description' => 'Фільтрувати за валютою.',
                'example' => 'UAH',
            ],
            'min_price' => [
                'description' => 'Мінімальна ціна для фільтрації.',
                'example' => 100,
            ],
            'max_price' => [
                'description' => 'Максимальна ціна для фільтрації.',
                'example' => 500,
            ],
            'duration_days' => [
                'description' => 'Тривалість тарифу в днях для фільтрації.',
                'example' => 30,
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
                'example' => 'price',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc або desc).',
                'example' => 'asc',
            ],
        ];
    }
}
