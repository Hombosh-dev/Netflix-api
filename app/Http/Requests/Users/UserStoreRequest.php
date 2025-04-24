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
}
