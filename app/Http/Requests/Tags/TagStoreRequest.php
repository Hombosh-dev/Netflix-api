<?php

namespace App\Http\Requests\Tags;

use App\Models\Tag;
use App\Rules\FileOrString;
use Illuminate\Foundation\Http\FormRequest;

class TagStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Tag::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:tags,name'],
            'description' => ['required', 'string', 'max:512'],
            'is_genre' => ['sometimes', 'boolean'],
            'image' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 5120)],
            'aliases' => ['nullable', 'array'],
            'aliases.*' => ['string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:128', 'unique:tags,slug'],
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
                'description' => 'Назва тегу.',
                'example' => 'Фантастика',
            ],
            'description' => [
                'description' => 'Опис тегу.',
                'example' => 'Жанр кіно, що використовує фантастичні елементи...',
            ],
            'is_genre' => [
                'description' => 'Чи є тег жанром.',
                'example' => true,
            ],
            'image' => [
                'description' => 'Зображення тегу (файл або URL, необов\'язково).',
                'example' => 'https://example.com/images/fantasy.jpg',
            ],
            'aliases' => [
                'description' => 'Масив альтернативних назв тегу.',
                'example' => ['Фентезі', 'Fantasy'],
            ],
            'slug' => [
                'description' => 'Унікальний ідентифікатор тегу для URL.',
                'example' => 'fantasy',
            ],
            'meta_title' => [
                'description' => 'SEO заголовок.',
                'example' => 'Фантастика - фільми та серіали',
            ],
            'meta_description' => [
                'description' => 'SEO опис.',
                'example' => 'Дивіться найкращі фільми та серіали в жанрі фантастики.',
            ],
            'meta_image' => [
                'description' => 'SEO зображення (файл або URL).',
                'example' => 'https://example.com/images/fantasy-meta.jpg',
            ],
        ];
    }
}
