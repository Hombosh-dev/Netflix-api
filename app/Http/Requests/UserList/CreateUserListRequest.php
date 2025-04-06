<?php

namespace App\Http\Requests\UserList;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserListRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'        => 'required|string',
            'listable_type'  => 'required|string',
            'listable_id'    => 'required|string',
            'type'           => 'required|string',
        ];
    }
}
