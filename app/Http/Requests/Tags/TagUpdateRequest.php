<?php

namespace App\Http\Requests\Tags;

use App\Models\Tag;
use App\Rules\FileOrString;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tag = $this->route('tag');

        return $this->user()->can('update', $tag);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tag = $this->route('tag');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('tags', 'name')->ignore($tag)],
            'description' => ['sometimes', 'string', 'max:512'],
            'is_genre' => ['sometimes', 'boolean'],
            'image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 5120)],
            'aliases' => ['nullable', 'array'],
            'aliases.*' => ['string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:128', Rule::unique('tags', 'slug')->ignore($tag)],
            'meta_title' => ['nullable', 'string', 'max:128'],
            'meta_description' => ['nullable', 'string', 'max:192'],
            'meta_image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 5120)],
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Convert JSON string to array
        if ($this->has('aliases') && is_string($this->input('aliases'))) {
            $this->merge([
                'aliases' => json_decode($this->input('aliases'), true) ?? []
            ]);
        }
    }
}
