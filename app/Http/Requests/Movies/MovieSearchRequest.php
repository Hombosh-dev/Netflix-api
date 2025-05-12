<?php

namespace App\Http\Requests\Movies;

use App\Enums\Kind;
use App\Enums\Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class MovieSearchRequest extends FormRequest
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
            'sort' => ['sometimes', 'string', 'in:name,created_at,imdb_score,first_air_date'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'kind' => ['sometimes', new Enum(Kind::class)],
            'status' => ['sometimes', new Enum(Status::class)],
            'min_score' => ['sometimes', 'numeric', 'min:0', 'max:10'],
            'max_score' => ['sometimes', 'numeric', 'min:0', 'max:10'],
            'studio_id' => ['sometimes', 'string', 'exists:studios,id'],
            'tag_id' => ['sometimes', 'string', 'exists:tags,id'],
            'person_id' => ['sometimes', 'string', 'exists:people,id'],
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
                'description' => 'Пошуковий запит для фільтрації фільмів.',
                'example' => 'Інтерстеллар',
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
                'example' => 'created_at',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc - за зростанням, desc - за спаданням).',
                'example' => 'desc',
            ],
            'kind' => [
                'description' => 'Тип контенту (MOVIE - фільм, TV_SERIES - серіал, тощо).',
                'example' => 'MOVIE',
            ],
            'status' => [
                'description' => 'Статус контенту (RELEASED - випущено, IN_PRODUCTION - у виробництві, тощо).',
                'example' => 'RELEASED',
            ],
            'min_score' => [
                'description' => 'Мінімальний рейтинг IMDb для фільтрації.',
                'example' => 7.5,
            ],
            'max_score' => [
                'description' => 'Максимальний рейтинг IMDb для фільтрації.',
                'example' => 10,
            ],
            'studio_id' => [
                'description' => 'ID студії для фільтрації фільмів.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'tag_id' => [
                'description' => 'ID тегу для фільтрації фільмів.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'person_id' => [
                'description' => 'ID персони для фільтрації фільмів.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
        ];
    }
}
