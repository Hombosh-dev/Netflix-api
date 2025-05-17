<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation' => ['required', 'string'],
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
                'description' => 'Ім\'я користувача.',
                'example' => 'John Doe',
            ],
            'email' => [
                'description' => 'Електронна пошта користувача (має бути унікальною).',
                'example' => 'john.doe@example.com',
            ],
            'password' => [
                'description' => 'Пароль користувача.',
                'example' => 'StrongPassword123!',
            ],
            'password_confirmation' => [
                'description' => 'Підтвердження пароля.',
                'example' => 'StrongPassword123!',
            ],
        ];
    }
}
