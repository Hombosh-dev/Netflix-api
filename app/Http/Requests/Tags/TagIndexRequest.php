<?php

namespace App\Http\Requests\Tags;

use Illuminate\Foundation\Http\FormRequest;

class TagIndexRequest extends FormRequest
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
            'q' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:name,created_at,movies_count'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'is_genre' => ['sometimes', 'boolean'],
            'has_movies' => ['sometimes', 'boolean'],

            // Multiple values support
            'movie_ids' => ['sometimes', 'array'],
            'movie_ids.*' => ['sometimes', 'string', 'exists:movies,id'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Convert comma-separated values to arrays
        $this->convertCommaSeparatedToArray('movie_ids');
    }

    /**
     * Convert comma-separated string to array
     *
     * @param  string  $field
     * @return void
     */
    private function convertCommaSeparatedToArray(string $field): void
    {
        if ($this->has($field) && is_string($this->input($field))) {
            $this->merge([
                $field => explode(',', $this->input($field))
            ]);
        }
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
                'description' => 'Пошуковий запит для фільтрації тегів за назвою.',
                'example' => 'Драма',
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
                'description' => 'Поле для сортування результатів (name - за назвою, created_at - за датою створення, movies_count - за кількістю фільмів).',
                'example' => 'name',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc - за зростанням, desc - за спаданням).',
                'example' => 'asc',
            ],
            'is_genre' => [
                'description' => 'Фільтр для відображення тільки тегів, які є жанрами.',
                'example' => true,
            ],
            'has_movies' => [
                'description' => 'Фільтр для відображення тільки тегів, які мають фільми.',
                'example' => true,
            ],
            'movie_ids' => [
                'description' => 'Фільтр за ID фільмів, які мають ці теги. Можна передати як масив, так і через кому.',
                'example' => ['01HN5PXMEH6SDMF0KAVSW1DYTY', '01HN5PXMEH6SDMF0KAVSW1DYTZ'],
            ],
        ];
    }
}
