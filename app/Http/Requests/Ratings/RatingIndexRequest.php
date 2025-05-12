<?php

namespace App\Http\Requests\Ratings;

use App\Models\Rating;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RatingIndexRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:number,created_at'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            'movie_id' => ['sometimes', 'string', 'exists:movies,id'],
            'min_rating' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'max_rating' => ['sometimes', 'integer', 'min:1', 'max:10'],
            'has_review' => ['sometimes', 'boolean'],
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
            'q' => [
                'description' => 'Пошуковий запит для фільтрації рейтингів.',
                'example' => 'Чудовий фільм',
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
                'example' => 'number',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc або desc).',
                'example' => 'desc',
            ],
            'user_id' => [
                'description' => 'ID користувача, чиї рейтинги потрібно отримати.',
                'example' => '',
            ],
            'movie_id' => [
                'description' => 'ID фільму, для якого потрібно отримати рейтинги.',
                'example' => '',
            ],
            'min_rating' => [
                'description' => 'Мінімальний рейтинг для фільтрації.',
                'example' => 1,
            ],
            'max_rating' => [
                'description' => 'Максимальний рейтинг для фільтрації.',
                'example' => 10,
            ],
            'has_review' => [
                'description' => 'Фільтрувати рейтинги з відгуками.',
                'example' => true,
            ],
        ];
    }
}
