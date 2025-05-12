<?php

namespace App\Http\Requests\Tariffs;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TariffStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Tariff::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'features' => ['required', 'array'],
            'features.*' => ['string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'slug' => ['required', 'string', 'max:128', 'unique:tariffs,slug'],
            'meta_title' => ['nullable', 'string', 'max:128'],
            'meta_description' => ['nullable', 'string', 'max:376'],
            'meta_image' => ['nullable', 'string', 'max:2048', 'url'],
        ];
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
                'description' => 'Назва тарифу.',
                'example' => 'Преміум',
            ],
            'description' => [
                'description' => 'Опис тарифу.',
                'example' => 'Повний доступ до всіх фільмів та серіалів без реклами.',
            ],
            'price' => [
                'description' => 'Ціна тарифу.',
                'example' => 199.99,
            ],
            'currency' => [
                'description' => 'Валюта тарифу (3 символи).',
                'example' => 'UAH',
            ],
            'duration_days' => [
                'description' => 'Тривалість тарифу в днях.',
                'example' => 30,
            ],
            'features' => [
                'description' => 'Масив особливостей тарифу.',
                'example' => [
                    'Доступ до всіх фільмів',
                    'Без реклами',
                    'Перегляд на 5 пристроях',
                ],
            ],
            'is_active' => [
                'description' => 'Чи є тариф активним.',
                'example' => true,
            ],
            'slug' => [
                'description' => 'Унікальний ідентифікатор тарифу для URL.',
                'example' => 'premium',
            ],
            'meta_title' => [
                'description' => 'SEO заголовок (необов’язково).',
                'example' => 'Преміум тариф - Netflix',
            ],
            'meta_description' => [
                'description' => 'SEO опис (необов’язково).',
                'example' => 'Отримайте повний доступ до всіх фільмів та серіалів без реклами з нашим преміум тарифом.',
            ],
            'meta_image' => [
                'description' => 'URL зображення для SEO (необов’язково).',
                'example' => 'https://example.com/images/premium-tariff.jpg',
            ],
        ];
    }
}
