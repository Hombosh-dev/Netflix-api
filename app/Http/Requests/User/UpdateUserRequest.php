<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id ?? null;
        return [
            'name'      => 'sometimes|required|string|max:255',
            'email'     => "sometimes|required|email|unique:users,email,{$userId}",
            'password'  => 'sometimes|required|string|min:8',
            'role'      => 'sometimes|required|string',
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
