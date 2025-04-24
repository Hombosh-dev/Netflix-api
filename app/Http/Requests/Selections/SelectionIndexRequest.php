<?php

namespace App\Http\Requests\Selections;

use Illuminate\Foundation\Http\FormRequest;

class SelectionIndexRequest extends FormRequest
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
            'sort' => ['sometimes', 'string', 'in:name,created_at,movies_count,user_lists_count'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'is_published' => ['sometimes', 'boolean'],
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            'has_movies' => ['sometimes', 'boolean'],
            'has_persons' => ['sometimes', 'boolean'],
            
            // Multiple values support
            'movie_ids' => ['sometimes', 'array'],
            'movie_ids.*' => ['sometimes', 'string', 'exists:movies,id'],
            'person_ids' => ['sometimes', 'array'],
            'person_ids.*' => ['sometimes', 'string', 'exists:people,id'],
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
        $this->convertCommaSeparatedToArray('person_ids');
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
