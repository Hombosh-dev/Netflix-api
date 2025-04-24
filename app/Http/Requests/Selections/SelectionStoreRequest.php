<?php

namespace App\Http\Requests\Selections;

use App\Models\Selection;
use App\Rules\FileOrString;
use Illuminate\Foundation\Http\FormRequest;

class SelectionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Selection::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            'is_published' => ['sometimes', 'boolean'],
            'movie_ids' => ['sometimes', 'array'],
            'movie_ids.*' => ['string', 'exists:movies,id'],
            'person_ids' => ['sometimes', 'array'],
            'person_ids.*' => ['string', 'exists:people,id'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:selections,slug'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
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
