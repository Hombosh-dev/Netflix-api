<?php

namespace App\Http\Requests\Persons;

use App\Enums\Gender;
use App\Enums\PersonType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PersonIndexRequest extends FormRequest
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
            'sort' => ['sometimes', 'string', 'in:name,created_at,birth_date,movies_count'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],

            // Multiple values support
            'types' => ['sometimes', 'array'],
            'types.*' => ['sometimes', new Enum(PersonType::class)],
            'genders' => ['sometimes', 'array'],
            'genders.*' => ['sometimes', new Enum(Gender::class)],
            'movie_ids' => ['sometimes', 'array'],
            'movie_ids.*' => ['sometimes', 'string', 'exists:movies,id'],

            // Age range
            'min_age' => ['sometimes', 'integer', 'min:0', 'max:150'],
            'max_age' => ['sometimes', 'integer', 'min:0', 'max:150'],
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
        $this->convertCommaSeparatedToArray('types');
        $this->convertCommaSeparatedToArray('genders');
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
                'description' => 'Пошуковий запит для фільтрації персон за іменем.',
                'example' => '',
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
                'description' => 'Поле для сортування результатів (name - за іменем, created_at - за датою створення, birth_date - за датою народження, movies_count - за кількістю фільмів).',
                'example' => 'name',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc - за зростанням, desc - за спаданням).',
                'example' => 'asc',
            ],
            'types' => [
                'description' => 'Фільтр за типами персон (actor - актор, director - режисер, тощо). Можна передати як масив, так і через кому.',
                'example' => ['actor', 'director'],
            ],
            'genders' => [
                'description' => 'Фільтр за статтю (male - чоловіча, female - жіноча, other - інша). Можна передати як масив, так і через кому.',
                'example' => ['male', 'female'],
            ],
            'movie_ids' => [
                'description' => 'Фільтр за ID фільмів, в яких брала участь персона. Можна передати як масив, так і через кому.',
                'example' => ['01HN5PXMEH6SDMF0KAVSW1DYTY', '01HN5PXMEH6SDMF0KAVSW1DYTZ'],
            ],
            'min_age' => [
                'description' => 'Мінімальний вік персони.',
                'example' => 16,
            ],
            'max_age' => [
                'description' => 'Максимальний вік персони.',
                'example' => 60,
            ],
        ];
    }
}
