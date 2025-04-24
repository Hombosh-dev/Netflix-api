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
}
