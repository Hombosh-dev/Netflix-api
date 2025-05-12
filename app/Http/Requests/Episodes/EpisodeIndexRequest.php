<?php

namespace App\Http\Requests\Episodes;

use App\DTOs\Episodes\EpisodeIndexDTO;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EpisodeIndexRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'movie_id' => ['sometimes', 'string'],
            'aired_after' => ['sometimes', 'date'],
            'include_filler' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', 'string', Rule::in(['number', 'air_date', 'created_at'])],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        return [];
    }

    /**
     * Get the query parameters for the request.
     *
     * @return array
     */
    public function queryParameters(): array
    {
        return [
            'movie_id' => [
                'description' => 'ID фільму або серіалу, для якого потрібно отримати епізоди.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'aired_after' => [
                'description' => 'Фільтр для отримання епізодів, які вийшли після вказаної дати.',
                'example' => '2001-01-01',
            ],
            'include_filler' => [
                'description' => 'Чи включати філлерні епізоди (епізоди, які не впливають на основний сюжет).',
                'example' => true,
            ],
            'sort' => [
                'description' => 'Поле для сортування результатів (number - за номером епізоду, air_date - за датою виходу, created_at - за датою створення).',
                'example' => 'air_date',
            ],
            'direction' => [
                'description' => 'Напрямок сортування (asc - за зростанням, desc - за спаданням).',
                'example' => 'desc',
            ],
            'page' => [
                'description' => 'Номер сторінки для пагінації.',
                'example' => 1,
            ],
            'per_page' => [
                'description' => 'Кількість елементів на сторінці.',
                'example' => 15,
            ],
        ];
    }
}
