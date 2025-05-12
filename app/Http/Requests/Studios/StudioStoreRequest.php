<?php

namespace App\Http\Requests\Studios;

use App\Models\Studio;
use App\Rules\FileOrString;
use Illuminate\Foundation\Http\FormRequest;

class StudioStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Studio::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:studios,name'],
            'description' => ['required', 'string', 'max:512'],
            'image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 5120)],
            'aliases' => ['nullable', 'array'],
            'aliases.*' => ['string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:128', 'unique:studios,slug'],
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
}
