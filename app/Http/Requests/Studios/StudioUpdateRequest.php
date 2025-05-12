<?php

namespace App\Http\Requests\Studios;

use App\Models\Studio;
use App\Rules\FileOrString;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudioUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $studio = $this->route('studio');

        return $this->user()->can('update', $studio);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $studio = $this->route('studio');

        return [
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('studios', 'name')->ignore($studio)],
            'description' => ['sometimes', 'string', 'max:512'],
            'image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 5120)],
            'aliases' => ['nullable', 'array'],
            'aliases.*' => ['string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:128', Rule::unique('studios', 'slug')->ignore($studio)],
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

    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [
            'name' => [
                'description' => 'Назва студії.',
                'example' => 'Warner Bros. Pictures',
            ],
            'description' => [
                'description' => 'Опис студії.',
                'example' => 'Американська кіностудія, одна з найбільших у світі...',
            ],
            'image' => [
                'description' => 'Логотип студії (файл або URL, необов\'язково).',
                'example' => 'https://example.com/images/warner-bros.jpg',
            ],
            'aliases' => [
                'description' => 'Масив альтернативних назв студії.',
                'example' => ['WB', 'Warner Brothers'],
            ],
            'slug' => [
                'description' => 'Унікальний ідентифікатор студії для URL.',
                'example' => 'warner-bros-pictures',
            ],
            'meta_title' => [
                'description' => 'SEO заголовок.',
                'example' => 'Warner Bros. Pictures - фільми та історія',
            ],
            'meta_description' => [
                'description' => 'SEO опис.',
                'example' => 'Дізнайтеся більше про Warner Bros. Pictures, її фільми та історію.',
            ],
            'meta_image' => [
                'description' => 'SEO зображення (файл або URL).',
                'example' => 'https://example.com/images/warner-bros-meta.jpg',
            ],
        ];
    }

    /**
     * Get the URL parameters for the request.
     *
     * @return array
     */
    public function urlParameters()
    {
        return [
            'studio' => [
                'description' => 'Slug студії, яку потрібно оновити.',
                'example' => 'warner-bros-pictures-abc123',
            ],
        ];
    }
}
