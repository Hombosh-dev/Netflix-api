<?php

namespace App\Http\Requests\UserList;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id'        => 'sometimes|required|string',
            'listable_type'  => 'sometimes|required|string',
            'listable_id'    => 'sometimes|required|string',
            'type'           => 'sometimes|required|string',
        ];
    }
}
