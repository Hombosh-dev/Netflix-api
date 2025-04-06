<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8',
            'role'      => 'required|string',
            'gender'    => 'nullable|string',
            'avatar'    => 'nullable|string|max:255',
            'backdrop'  => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'birthday'  => 'nullable|date',
            'allow_adult' => 'sometimes|boolean',
            'is_auto_next' => 'sometimes|boolean',
            'is_auto_play' => 'sometimes|boolean',
            'is_auto_skip_intro' => 'sometimes|boolean',
            'is_private_favorites' => 'sometimes|boolean',
        ];
    }
}
