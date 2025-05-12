<?php

namespace App\Http\Requests\Users;

use App\Enums\Gender;
use App\Enums\Role;
use App\Rules\FileOrString;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->route('user');

        // Для генерації документації - якщо користувач не знайдений, дозволяємо доступ
        if (!$user) {
            return true;
        }

        // Use the policy to check if the user can update the user
        return $this->user() && $this->user()->can('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        // Для генерації документації - якщо користувач не знайдений, використовуємо порожній ID
        $userId = $user ? $user->id : null;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                $userId ? Rule::unique('users')->ignore($userId) : Rule::unique('users')
            ],
            'password' => ['sometimes', 'string', Password::defaults(), 'confirmed'],
            'role' => [
                'sometimes',
                new Enum(Role::class),
                // Only admins can change roles (handled by UserPolicy::before)
            ],
            'gender' => ['sometimes', 'nullable', new Enum(Gender::class)],
            'avatar' => ['sometimes', 'nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 5120)],
            'backdrop' => ['sometimes', 'nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'birthday' => ['sometimes', 'nullable', 'date'],
            'allow_adult' => ['sometimes', 'boolean'],
            'is_auto_next' => ['sometimes', 'boolean'],
            'is_auto_play' => ['sometimes', 'boolean'],
            'is_auto_skip_intro' => ['sometimes', 'boolean'],
            'is_private_favorites' => ['sometimes', 'boolean'],
            'is_banned' => [
                'sometimes',
                'boolean',
                // Only admins can ban/unban users (handled by UserPolicy::before)
            ],
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
                'description' => "Ім'я користувача",
                'example' => 'Іван Петренко',
            ],
            'email' => [
                'description' => 'Електронна пошта користувача',
                'example' => 'user@example.com',
            ],
            'password' => [
                'description' => 'Новий пароль користувача',
                'example' => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'Підтвердження нового пароля',
                'example' => 'password123',
            ],
            'role' => [
                'description' => 'Роль користувача (тільки для адміністраторів)',
                'example' => 'user',
            ],
            'gender' => [
                'description' => 'Стать користувача',
                'example' => 'male',
            ],
            'avatar' => [
                'description' => 'Аватар користувача (URL або файл)',
                'example' => 'https://example.com/avatar.jpg',
            ],
            'backdrop' => [
                'description' => 'Фонове зображення профілю користувача (URL або файл)',
                'example' => 'https://example.com/backdrop.jpg',
            ],
            'description' => [
                'description' => 'Опис профілю користувача',
                'example' => 'Люблю дивитися фільми та серіали',
            ],
            'birthday' => [
                'description' => 'Дата народження користувача',
                'example' => '1990-01-01',
            ],
            'allow_adult' => [
                'description' => 'Дозволити контент для дорослих',
                'example' => true,
            ],
            'is_auto_next' => [
                'description' => 'Автоматично переходити до наступного епізоду',
                'example' => true,
            ],
            'is_auto_play' => [
                'description' => 'Автоматично відтворювати трейлери',
                'example' => true,
            ],
            'is_auto_skip_intro' => [
                'description' => 'Автоматично пропускати вступ',
                'example' => true,
            ],
            'is_private_favorites' => [
                'description' => 'Зробити список улюблених приватним',
                'example' => false,
            ],
            'is_banned' => [
                'description' => 'Заблокувати користувача (тільки для адміністраторів)',
                'example' => false,
            ],
        ];
    }
}
