<?php

namespace App\Http\Requests\UserLists;

use App\Enums\UserListType;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Selection;
use App\Models\Tag;
use App\Models\UserList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UserListIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', UserList::class);
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
            'sort' => ['sometimes', 'string', 'in:created_at,updated_at'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],
            'user_id' => ['sometimes', 'string', 'exists:users,id'],
            
            // Multiple values support
            'types' => ['sometimes', 'array'],
            'types.*' => ['sometimes', new Enum(UserListType::class)],
            
            // Listable filters
            'listable_type' => [
                'sometimes', 
                'string', 
                Rule::in([
                    Movie::class,
                    Episode::class,
                    Person::class,
                    Tag::class,
                    Selection::class,
                ])
            ],
            'listable_id' => ['sometimes', 'string'],
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
