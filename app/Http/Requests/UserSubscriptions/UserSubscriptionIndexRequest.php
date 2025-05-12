<?php

namespace App\Http\Requests\UserSubscriptions;

use App\Models\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserSubscriptionIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', UserSubscription::class);
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
            'is_active' => ['sometimes', 'boolean'],
            'auto_renew' => ['sometimes', 'boolean'],
            'start_date_from' => ['sometimes', 'date'],
            'start_date_to' => ['sometimes', 'date', 'after_or_equal:start_date_from'],
            'end_date_from' => ['sometimes', 'date'],
            'end_date_to' => ['sometimes', 'date', 'after_or_equal:end_date_from'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', Rule::in(['created_at', 'updated_at', 'start_date', 'end_date'])],
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
                'description' => 'ID користувача для фільтрації підписок.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'tariff_id' => [
                'description' => 'ID тарифу для фільтрації підписок.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'is_active' => [
                'description' => 'Фільтрувати за статусом активності.',
                'example' => true,
            ],
            'auto_renew' => [
                'description' => 'Фільтрувати за автоматичним продовженням.',
                'example' => true,
            ],
            'start_date_from' => [
                'description' => 'Початкова дата для фільтрації за датою початку.',
                'example' => '2023-01-01',
            ],
            'start_date_to' => [
                'description' => 'Кінцева дата для фільтрації за датою початку.',
                'example' => '2023-12-31',
            ],
            'end_date_from' => [
                'description' => 'Початкова дата для фільтрації за датою закінчення.',
                'example' => '2023-01-01',
            ],
            'end_date_to' => [
                'description' => 'Кінцева дата для фільтрації за датою закінчення.',
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
                'example' => 'start_date',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc або desc).',
                'example' => 'desc',
            ],
        ];
    }
}
