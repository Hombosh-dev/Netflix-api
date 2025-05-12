<?php

namespace App\Http\Requests\Ratings;

use App\Models\Rating;
use Illuminate\Foundation\Http\FormRequest;

class RatingStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Rating::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movie_id' => ['required', 'string', 'exists:movies,id'],
            'number' => ['required', 'integer', 'min:1', 'max:10'],
            'review' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'movie_id' => [
                'description' => 'ID фільму, якому виставляється рейтинг.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'number' => [
                'description' => 'Числовий рейтинг від 1 до 10.',
                'example' => 8,
            ],
            'review' => [
                'description' => 'Текстовий відгук про фільм (необов’язково).',
                'example' => 'Дуже цікавий фільм з чудовою грою акторів та захоплюючим сюжетом.',
            ],
        ];
    }
}
