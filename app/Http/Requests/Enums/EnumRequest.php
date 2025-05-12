<?php

namespace App\Http\Requests\Enums;

use Illuminate\Foundation\Http\FormRequest;

class EnumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'locale' => ['sometimes', 'string', 'in:en,uk'],
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
            'locale' => [
                'description' => 'Мова для локалізації енумерацій (en - англійська, uk - українська).',
                'example' => 'uk',
            ],
        ];
    }
}
