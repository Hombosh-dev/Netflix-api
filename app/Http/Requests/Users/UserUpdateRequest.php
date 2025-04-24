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

        // Use the policy to check if the user can update the user
        return $this->user()->can('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
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
}
