<?php

namespace App\Http\Requests\Studios;

use Illuminate\Foundation\Http\FormRequest;

class StudioSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:name,created_at,movies_count'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'has_movies' => ['sometimes', 'boolean'],
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
            'page' => [
                'description' => 'Номер сторінки для пагінації.',
                'example' => 1,
            ],
            'per_page' => [
                'description' => 'Кількість елементів на сторінці.',
                'example' => 15,
            ],
            'sort' => [
                'description' => 'Поле для сортування результатів (name - за назвою, created_at - за датою створення, movies_count - за кількістю фільмів).',
                'example' => 'name',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc - за зростанням, desc - за спаданням).',
                'example' => 'asc',
            ],
            'has_movies' => [
                'description' => 'Фільтр для відображення тільки студій, які мають фільми.',
                'example' => true,
            ],
        ];
    }
}
