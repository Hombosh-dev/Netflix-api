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
}
