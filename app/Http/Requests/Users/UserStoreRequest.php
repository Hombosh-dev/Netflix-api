<?php

namespace App\Http\Requests\Users;

use App\Enums\Gender;
use App\Enums\Role;
use App\Rules\FileOrString;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Use the policy to check if the user can create users
        return $this->user()->can('create', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'role' => ['required', new Enum(Role::class)],
            'gender' => ['nullable', new Enum(Gender::class)],
            'avatar' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 5120)],
            'backdrop' => ['nullable', new FileOrString(['image/jpeg', 'image/png', 'image/webp'], 10240)],
            'description' => ['nullable', 'string', 'max:1000'],
            'birthday' => ['nullable', 'date'],
            'allow_adult' => ['sometimes', 'boolean'],
            'is_auto_next' => ['sometimes', 'boolean'],
            'is_auto_play' => ['sometimes', 'boolean'],
            'is_auto_skip_intro' => ['sometimes', 'boolean'],
            'is_private_favorites' => ['sometimes', 'boolean'],
            'is_banned' => ['sometimes', 'boolean'],
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
                'description' => 'Ім\'\u044f користувача.',
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
            'role' => [
                'description' => 'Роль користувача.',
                'example' => 'USER',
            ],
            'gender' => [
                'description' => 'Стать користувача (необов\'язково).',
                'example' => 'MALE',
            ],
            'avatar' => [
                'description' => 'Аватар користувача (файл або URL, необов\'язково).',
                'example' => 'https://example.com/avatar.jpg',
            ],
            'backdrop' => [
                'description' => 'Фонове зображення користувача (файл або URL, необов\'язково).',
                'example' => 'https://example.com/backdrop.jpg',
            ],
            'description' => [
                'description' => 'Опис профілю користувача (необов\'язково).',
                'example' => 'Люблю дивитись фільми та серіали у вільний час.',
            ],
            'birthday' => [
                'description' => 'Дата народження користувача (необов\'язково).',
                'example' => '1990-01-01',
            ],
            'allow_adult' => [
                'description' => 'Чи дозволений контент для дорослих.',
                'example' => true,
            ],
            'is_auto_next' => [
                'description' => 'Чи автоматично переходити до наступного епізоду.',
                'example' => true,
            ],
            'is_auto_play' => [
                'description' => 'Чи автоматично відтворювати відео.',
                'example' => true,
            ],
            'is_auto_skip_intro' => [
                'description' => 'Чи автоматично пропускати інтро.',
                'example' => true,
            ],
            'is_private_favorites' => [
                'description' => 'Чи є список улюблених приватним.',
                'example' => false,
            ],
            'is_banned' => [
                'description' => 'Чи заблокований користувач.',
                'example' => false,
            ],
        ];
    }
}
