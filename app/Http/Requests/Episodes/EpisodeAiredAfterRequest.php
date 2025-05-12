<?php

namespace App\Http\Requests\Episodes;

use App\DTOs\Episodes\EpisodeIndexDTO;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EpisodeAiredAfterRequest extends FormRequest
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
            'include_filler' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', 'string', Rule::in(['number', 'air_date', 'created_at'])],
            'direction' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // No comma-separated values to convert for now
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
     * Get the URL parameters for the request.
     *
     * @return array
     */
    public function urlParameters()
    {
        return [
            'date' => [
                'description' => 'Дата, після якої вийшли епізоди (у форматі YYYY-MM-DD).',
                'example' => '2023-01-01',
            ],
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
