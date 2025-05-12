<?php

namespace App\Http\Requests\Movies;

use App\Enums\Kind;
use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class MovieIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort' => ['sometimes', 'string', 'in:name,created_at,imdb_score,first_air_date,duration,episodes_count'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],

            // Multiple values support
            'kinds' => ['sometimes', 'array'],
            'kinds.*' => ['sometimes', new Enum(Kind::class)],
            'statuses' => ['sometimes', 'array'],
            'statuses.*' => ['sometimes', new Enum(Status::class)],
            'studio_ids' => ['sometimes', 'array'],
            'studio_ids.*' => ['sometimes', 'string', 'exists:studios,id'],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['sometimes', 'string', 'exists:tags,id'],
            'person_ids' => ['sometimes', 'array'],
            'person_ids.*' => ['sometimes', 'string', 'exists:people,id'],
            'countries' => ['sometimes', 'array'],
            'countries.*' => ['sometimes', 'string', 'max:2'],

            // Score range
            'min_score' => ['sometimes', 'numeric', 'min:0', 'max:10'],
            'max_score' => ['sometimes', 'numeric', 'min:0', 'max:10'],

            // Year range
            'min_year' => ['sometimes', 'integer', 'min:1900', 'max:'.(date('Y') + 10)],
            'max_year' => ['sometimes', 'integer', 'min:1900', 'max:'.(date('Y') + 10)],

            // Duration range (in minutes)
            'min_duration' => ['sometimes', 'integer', 'min:1'],
            'max_duration' => ['sometimes', 'integer', 'min:1'],

            // Episodes count range
            'min_episodes_count' => ['sometimes', 'integer', 'min:1'],
            'max_episodes_count' => ['sometimes', 'integer', 'min:1'],
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
        $this->convertCommaSeparatedToArray('kinds');
        $this->convertCommaSeparatedToArray('statuses');
        $this->convertCommaSeparatedToArray('studio_ids');
        $this->convertCommaSeparatedToArray('tag_ids');
        $this->convertCommaSeparatedToArray('person_ids');
        $this->convertCommaSeparatedToArray('countries');
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
                'description' => 'Пошуковий запит для фільтрації фільмів.',
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
                'description' => 'Поле для сортування результатів (name, created_at, imdb_score, first_air_date, duration, episodes_count).',
                'example' => 'created_at',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc - за зростанням, desc - за спаданням).',
                'example' => 'desc',
            ],
            // Множинні значення
            'kinds' => [
                'description' => 'Типи контенту через кому (MOVIE - фільм, TV_SERIES - серіал, ANIMATED_SERIES - анімаційний серіал, тощо).',
                'example' => 'MOVIE,TV_SERIES',
            ],
            'statuses' => [
                'description' => 'Статуси контенту через кому (RELEASED - випущено, IN_PRODUCTION - у виробництві, тощо).',
                'example' => 'RELEASED,IN_PRODUCTION',
            ],
            'studio_ids' => [
                'description' => 'ID студій через кому для фільтрації фільмів.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY,01HN5PXMEH6SDMF0KAVSW1DYTZ',
            ],
            'tag_ids' => [
                'description' => 'ID тегів через кому для фільтрації фільмів.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY,01HN5PXMEH6SDMF0KAVSW1DYTZ',
            ],
            'person_ids' => [
                'description' => 'ID персон через кому для фільтрації фільмів.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY,01HN5PXMEH6SDMF0KAVSW1DYTZ',
            ],
            'countries' => [
                'description' => 'Коди країн через кому для фільтрації фільмів (ISO 3166-1 alpha-2).',
                'example' => 'US,UA,GB',
            ],
            // Діапазони значень
            'min_score' => [
                'description' => 'Мінімальний рейтинг IMDb для фільтрації.',
                'example' => 1,
            ],
            'max_score' => [
                'description' => 'Максимальний рейтинг IMDb для фільтрації.',
                'example' => 10,
            ],
            'min_year' => [
                'description' => 'Мінімальний рік випуску для фільтрації.',
                'example' => 2000,
            ],
            'max_year' => [
                'description' => 'Максимальний рік випуску для фільтрації.',
                'example' => 2023,
            ],
            'min_duration' => [
                'description' => 'Мінімальна тривалість у хвилинах для фільтрації.',
                'example' => 10,
            ],
            'max_duration' => [
                'description' => 'Максимальна тривалість у хвилинах для фільтрації.',
                'example' => 180,
            ],
            'min_episodes_count' => [
                'description' => 'Мінімальна кількість епізодів для фільтрації (для серіалів).',
                'example' => 1,
            ],
            'max_episodes_count' => [
                'description' => 'Максимальна кількість епізодів для фільтрації (для серіалів).',
                'example' => 24,
            ],
            // Додаткові параметри
            'is_published' => [
                'description' => 'Фільтрувати за статусом публікації.',
                'example' => true,
            ],
        ];
    }
}
