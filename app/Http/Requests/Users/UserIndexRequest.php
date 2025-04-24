<?php

namespace App\Http\Requests\Users;

use App\Enums\Gender;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UserIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', User::class);
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
            'sort' => ['sometimes', 'string', 'in:name,email,created_at,last_seen_at'],
            'direction' => ['sometimes', 'string', 'in:asc,desc'],

            // Multiple values support
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['sometimes', new Enum(Role::class)],
            'genders' => ['sometimes', 'array'],
            'genders.*' => ['sometimes', new Enum(Gender::class)],

            // Boolean filters
            'is_banned' => ['sometimes', 'boolean'],
            'is_verified' => ['sometimes', 'boolean'],

            // Date filters
            'last_seen_after' => ['sometimes', 'date'],
            'last_seen_before' => ['sometimes', 'date'],
            'created_after' => ['sometimes', 'date'],
            'created_before' => ['sometimes', 'date'],
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
        $this->convertCommaSeparatedToArray('roles');
        $this->convertCommaSeparatedToArray('genders');
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
