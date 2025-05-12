<?php

namespace App\Http\Requests\Tariffs;

use App\Models\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TariffUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tariff = $this->route('tariff');

        return $this->user()->can('update', $tariff);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tariff = $this->route('tariff');

        return [
            'name' => ['sometimes', 'string', 'max:128'],
            'description' => ['sometimes', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'duration_days' => ['sometimes', 'integer', 'min:1'],
            'features' => ['sometimes', 'array'],
            'features.*' => ['string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'slug' => ['sometimes', 'string', 'max:128', Rule::unique('tariffs', 'slug')->ignore($tariff)],
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
                'example' => 'Преміум Плюс',
            ],
            'description' => [
                'description' => 'Опис тарифу.',
                'example' => 'Повний доступ до всіх фільмів та серіалів без реклами та з можливістю завантаження.',
            ],
            'price' => [
                'description' => 'Ціна тарифу.',
                'example' => 249.99,
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
                    'Завантаження фільмів',
                ],
            ],
            'is_active' => [
                'description' => 'Чи є тариф активним.',
                'example' => true,
            ],
            'slug' => [
                'description' => 'Унікальний ідентифікатор тарифу для URL.',
                'example' => 'premium-plus',
            ],
            'meta_title' => [
                'description' => 'SEO заголовок (необов’язково).',
                'example' => 'Преміум Плюс тариф - Netflix',
            ],
            'meta_description' => [
                'description' => 'SEO опис (необов’язково).',
                'example' => 'Отримайте повний доступ до всіх фільмів та серіалів без реклами та з можливістю завантаження.',
            ],
            'meta_image' => [
                'description' => 'URL зображення для SEO (необов’язково).',
                'example' => 'https://example.com/images/premium-plus-tariff.jpg',
            ],
        ];
    }
}
