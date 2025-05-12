<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class NewPasswordRequest extends FormRequest
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
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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
            'token' => [
                'description' => 'Токен для відновлення пароля, отриманий електронною поштою.',
                'example' => 'a1b2c3d4e5f6g7h8i9j0',
            ],
            'email' => [
                'description' => 'Електронна пошта користувача, для якого відновлюється пароль.',
                'example' => 'user@example.com',
            ],
            'password' => [
                'description' => 'Новий пароль користувача.',
                'example' => 'NewStrongPassword123!',
            ],
            'password_confirmation' => [
                'description' => 'Підтвердження нового пароля.',
                'example' => 'NewStrongPassword123!',
            ],
        ];
    }
}
