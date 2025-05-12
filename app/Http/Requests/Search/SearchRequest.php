<?php

namespace App\Http\Requests\Search;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
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
            'q' => ['required', 'string', 'min:1'],
            'types' => ['sometimes', 'string'],
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
            'q' => [
                'description' => 'Пошуковий запит.',
                'example' => 'Матриця',
            ],
            'types' => [
                'description' => 'Типи контенту для пошуку, розділені комами (movies,people,studios,tags,selections).',
                'example' => 'movies,people',
            ],
        ];
    }
}
